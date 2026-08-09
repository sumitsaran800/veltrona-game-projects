importScripts('https://www.gstatic.com/firebasejs/10.8.1/firebase-app-compat.js');
importScripts('https://www.gstatic.com/firebasejs/10.8.1/firebase-messaging-compat.js');
importScripts('/webapi/kv/firebaseConfig.js');

let messaging = null;
let isFirebaseInitialized = false;
let firebaseConfigCache = null;
let firebaseInitPromise = null;


/* ------------------------------
 * 🧩 生命周期：install / activate
 * ------------------------------ */
self.addEventListener('install', (event) => {
    // 立即进入 waiting -> activate
    self.skipWaiting();
});

self.addEventListener('activate', (event) => {
    event.waitUntil((async () => {
        // 让新 SW 立即接管页面
        await self.clients.claim();

        // 🔥 关键：activate 预热（强制读取 IDB + 初始化 Firebase）
        try {
            console.log('[SW] activate → preload firebase');
            await ensureFirebaseReady();
            console.log('[SW] firebase preloaded');
        } catch (e) {
            console.error('[SW] preload firebase failed:', e);
            // 不要 throw，activate 仍然要完成；后续 push 还能重试
        }
    })());
});

async function ensureFirebaseReady() {
    if (isFirebaseInitialized) return true;

    if (!firebaseInitPromise) {
        firebaseInitPromise = (async () => {
            await initFirebase(); // 失败必须 throw
            return true;
        })().catch((e) => {
            // 失败允许重试
            firebaseInitPromise = null;
            throw e;
        });
    }
    return firebaseInitPromise;
}

/* ------------------------------
 * 🧭 事件映射表
 * ------------------------------ */
const eventNameMap = {
    notification_receive: 'NOTIFICATION_RECEIVE',
    notification_display: 'NOTIFICATION_DISPLAY',
    notification_click: 'NOTIFICATION_CLICK',
    notification_dismiss: 'NOTIFICATION_DISMISS',
    notification_open: 'NOTIFICATION_OPEN',
    notification_engagement: 'NOTIFICATION_ENGAGEMENT', // 保留未来扩展
};


/**
 * @param {string} type - 事件类型
 * @param {object} data - 附带数据
 * @param {object} [options]
 * @param {boolean} [options.focusExisting=true] - 是否聚焦已有页面
 * @param {boolean} [options.sameOriginOnly=true] - 仅发送给同源页面
 * @param {boolean} [options.openIfNotFound=false] - 若无页面是否新开
 * @param {string} [options.openUrl='/'] - 新开页面的默认URL
 */
async function sendToClients(type, data, options = {}) {
    const {
        focusExisting = true,
            sameOriginOnly = true,
            openIfNotFound = false,
            openUrl = '/'
    } = options;

    const clients = await self.clients.matchAll({
        type: 'window',
        includeUncontrolled: true
    });
    try {
        for (const client of clients) {
            if (sameOriginOnly && !client.url.includes(self.location.origin)) continue;
            client.postMessage({
                type,
                data
            });
        }
    } catch (error) {
        console.error('发送消息到客户端失败:', error);
    }
    return true;
}


// 接口如果拿不到可以通过h5存入indexDB再取
const initFirebase = () => {
    if (isFirebaseInitialized && firebaseConfigCache) {
        return;
    }
    firebase.initializeApp(_FIREBASE_CONFIG_);
    firebaseConfigCache = _FIREBASE_CONFIG_;
    isFirebaseInitialized = true;

    messaging = firebase.messaging();
    messaging.onBackgroundMessage(async (payload) => {
        return receiveBackgroundMessage(payload);
    });
}

/* ------------------------------
 * 📨 后台消息监听（Service Worker 收到推送）
 * ------------------------------ */

const receiveBackgroundMessage = async (payload) => {
    console.log('后台接受', payload);

    // 1️⃣ 通知接收埋点
    await sendToClients(eventNameMap.notification_receive, payload.data);

    const {
        title,
        body,
        image,
        targetLink,
        openPage
    } = payload.data || payload.notification || {};
    const data = payload.data || payload.notification || {};

    // 3️⃣ 显示通知
    await self.registration.showNotification(title || 'Notify', {
        body: body || '',
        image: image || '/logo.png',
        icon: image || '/logo.png',
        data,
        tag: data.messageId || Date.now().toString(), // 防止重复通知
    });

    // 4️⃣ 通知展示埋点
    await sendToClients(eventNameMap.notification_display, payload.data);
}


/* ------------------------------
 * 📤 初始化
 * ------------------------------ */

initFirebase()


/* ------------------------------
 * 📤 前台消息触发通知（页面调用）
 * ------------------------------ */

// 监听来自页面的消息
self.addEventListener('message', (event) => {
    console.log('Service Worker 收到消息:', event.data);
    if (event.data ? .type === 'FIREBASE_SHOW_NOTIFICATION') {
        const {
            title,
            body,
            image,
            openPage,
            targetLink,
            messageId
        } = event.data.payload || {};

        console.log('前台消息触发通知', event.data.payload);

        self.registration.showNotification(title || "Notify", {
            body,
            image: image || '/logo.png',
            icon: image || '/logo.png',
            data: event.data.payload
        });
    }
});

/* ------------------------------
 * ❌ 通知关闭事件
 * ------------------------------ */
self.addEventListener('notificationclose', (event) => {
    sendToClients(eventNameMap.notification_dismiss, { ...event.notification ? .data
    });
});

/* ------------------------------
 * ✅ 通知点击事件（统计 + 打开链接）
 * ------------------------------ */
self.addEventListener('notificationclick', (event) => {
    event.notification.close();
    const messageData = event.notification ? .data;

    const sent = false;

    event.waitUntil((async () => {
        const clients = await self.clients.matchAll({
            type: 'window',
            includeUncontrolled: true
        });
        let sent = false;
        try {
            for (const client of clients) {
                if (!client.url.includes(self.location.origin)) continue;

                if ('focus' in client) {
                    client.focus();
                }
                client.postMessage({
                    type: eventNameMap.notification_click,
                    data: { ...messageData
                    }
                });
                sent = true;
                if (self.clients.openWindow) {
                    // 将 messageData 的所有字段拼接到 URL 查询参数
                    const url = new URL(self.location.origin);
                    if (messageData && typeof messageData === 'object') {
                        Object.entries(messageData).forEach(([key, value]) => {
                            if (value !== undefined && value !== null) {
                                url.searchParams.set(key, value);
                            }
                        });
                    }
                    await self.clients.openWindow(url.toString());
                }
            }
        } catch (error) {
            sent = false;
            console.error('发送消息到客户端失败:', error);
        }

        // 如果没找到页面且允许新开窗口
        if (self.clients.openWindow) {
            // 将 messageData 的所有字段拼接到 URL 查询参数
            const url = new URL(self.location.origin);
            if (messageData && typeof messageData === 'object') {
                Object.entries(messageData).forEach(([key, value]) => {
                    if (value !== undefined && value !== null) {
                        url.searchParams.set(key, value);
                    }
                });
            }
            await self.clients.openWindow(url.toString());
        }
        sendToClients(eventNameMap.notification_open, { ...messageData
        });
    })())
});