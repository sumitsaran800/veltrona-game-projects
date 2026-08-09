import{r as ref}from"./index-xnhGKCfe.js";

const API_BASE="/api";
function token(){return localStorage.getItem("ar_p_t")||new URLSearchParams(location.search).get("token")||""}
async function post(path,data={}){
  const headers={"Content-Type":"application/json"};
  const tk=localStorage.getItem("token")||localStorage.getItem("Authorization")||localStorage.getItem("accessToken")||"";
  if(tk) headers.Authorization=tk.startsWith("Bearer ")?tk:"Bearer "+tk;
  try{const r=await fetch(API_BASE+path,{method:"POST",headers,body:JSON.stringify(data)});return await r.json()}catch(e){return{code:-1,msg:e&&e.message?e.message:"Network error",data:null}}
}
function fmtTime(v){v=Math.max(0,Number(v||0));const m=Math.floor(v/60),s=v%60;return String(m).padStart(2,"0")+":"+String(s).padStart(2,"0")}
function money(v){return "₹"+Number(v||0).toFixed(2)}
function normalizeInfo(d){
  const c=typeof d.customerInfo==="string"?safeJson(d.customerInfo):d.customerInfo||{};
  const r=typeof d.rechargeInfo==="string"?safeJson(d.rechargeInfo):d.rechargeInfo||{};
  const qr=d.qrCodeUrl||d.qrCode||d.qrcode||r.qrCode||r.qrcode||c.qrCode||c.qrcode||"";
  const payUrl=d.payUrl||r.payUrl||c.payUrl||"";
  const expire=Math.max(0,Math.floor(((Number(d.expiredTime||0)||Date.now()+900000)-Date.now())/1000));
  return{...d,customerInfo:c,rechargeInfo:r,qrCode:qr,qrcode:qr,qrCodeUrl:qr,payUrl,paymentExpireTime:expire,amount:Number(d.amount||d.rechargeAmount||0)}
}
function safeJson(s){try{return JSON.parse(s||"{}")}catch{return{}}}
function noop(){}
function useArUpi(){
  const pageData=ref({type:0,info:{amount:0,paymentExpireTime:900,qrCode:"",qrcode:"",payUrl:""}});
  const qrcode=ref("");
  const payList=ref([{name:"PhonePe"},{name:"Paytm"}]);
  const handleToPayType=ref("");
  const reasonList=ref([{id:1,reason:"I want to cancel"},{id:2,reason:"Payment problem"}]);
  const confirmShow=ref(false);
  const from=ref({checked:-1,text:""});
  async function getInfo(){
    const orderNo=token();
    let res={code:-1,data:null};
    if(orderNo) res=await post("/Recharge/GetLocalRechargeOrderDetail",{orderNo,rechargeNumber:orderNo,merchantOrderNo:orderNo});
    if(res&&res.code===0&&res.data){
      const info=normalizeInfo(res.data);
      pageData.value={type:info.state===4||info.rechargeState==="Cancel"?1:(info.rechargeState==="PendingReview"?3:0),info};
      qrcode.value=info.qrCodeUrl||info.qrCode||info.qrcode||"";
    } else {
      pageData.value={type:0,info:{amount:0,paymentExpireTime:900,qrCode:"",qrcode:"",payUrl:""}};
      qrcode.value="";
    }
  }
  function getText(e){from.value.text=e&&e.target?e.target.value:from.value.text}
  function getChecked(v){from.value.checked=v}
  function handleToPaytmmp(item){handleToPayType.value=item&&item.name?item.name:""; const url=(pageData.value.info||{}).payUrl; if(url&&url.startsWith("upi:")){try{location.href=url}catch{}}}
  async function getCancellationReasonList(){reasonList.value=reasonList.value}
  async function submitCancel(){confirmShow.value=false; const orderNo=token(); if(orderNo) await post("/Recharge/CancelLocalRecharge",{orderNo,rechargeNumber:orderNo,reason:from.value.text||"cancel"}); pageData.value.type=1}
  function onClick(){history.length>1?history.back():location.href="/wallet/recharge"}
  function pageView(){}
  function pageLeve(){}
  function stopFun(){}
  return{formatUpiTime:fmtTime,getFormatAmount:money,qrcode,getInfo,getText,getChecked,reasonList,confirmShow,payList,from,handleToPayType,handleToPaytmmp,getCancellationReasonList,submitCancel,onClick,stopFun,pageData,pageView,pageLeve}
}
function componentFallback(){return{}}
export default componentFallback;
export{useArUpi as u,componentFallback as P,componentFallback as a};
