CREATE TABLE IF NOT EXISTS users (
  id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  tenant_user_id VARCHAR(32) NOT NULL,
  username VARCHAR(120) NOT NULL,
  mobile VARCHAR(40) DEFAULT NULL,
  email VARCHAR(160) DEFAULT NULL,
  password_hash VARCHAR(255) NOT NULL,
  nickname VARCHAR(120) NOT NULL,
  photo VARCHAR(255) DEFAULT '1',
  real_name VARCHAR(120) DEFAULT NULL,
  balance DECIMAL(18,2) NOT NULL DEFAULT 0,
  safe_box DECIMAL(18,2) NOT NULL DEFAULT 0,
  vip_level INT NOT NULL DEFAULT 1,
  invite_code VARCHAR(20) DEFAULT NULL,
  role ENUM('user','admin') NOT NULL DEFAULT 'user',
  status TINYINT NOT NULL DEFAULT 1,
  login_session_token VARCHAR(80) DEFAULT '',
  withdraw_password_hash VARCHAR(255) DEFAULT '',
  ip_last VARCHAR(80) DEFAULT '',
  device_id VARCHAR(120) DEFAULT '',
  created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
  last_login_at DATETIME DEFAULT NULL,
  PRIMARY KEY (id),
  UNIQUE KEY uq_username (username),
  KEY idx_role (role)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
CREATE TABLE IF NOT EXISTS banners (
  id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  name VARCHAR(190) NOT NULL,
  icon_url VARCHAR(500) NOT NULL,
  jump_type INT NOT NULL DEFAULT 2,
  jump_detail VARCHAR(500) DEFAULT '',
  display_target INT NOT NULL DEFAULT 1,
  sort INT NOT NULL DEFAULT 0,
  status TINYINT NOT NULL DEFAULT 1,
  created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
CREATE TABLE IF NOT EXISTS game_categories (
  id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  category_code VARCHAR(80) NOT NULL,
  name_en VARCHAR(120) NOT NULL,
  name_hi VARCHAR(120) DEFAULT NULL,
  sort INT NOT NULL DEFAULT 0,
  img VARCHAR(500) DEFAULT NULL,
  selected_img VARCHAR(500) DEFAULT NULL,
  status TINYINT NOT NULL DEFAULT 1,
  PRIMARY KEY(id),
  UNIQUE KEY uq_cat (category_code)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
CREATE TABLE IF NOT EXISTS games (
  id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  category_code VARCHAR(80) DEFAULT NULL,
  vendor_code VARCHAR(80) DEFAULT NULL,
  game_id BIGINT DEFAULT NULL,
  game_code VARCHAR(120) NOT NULL,
  name VARCHAR(190) NOT NULL,
  img VARCHAR(500) DEFAULT NULL,
  sort BIGINT DEFAULT 0,
  rtp DECIMAL(8,2) DEFAULT 98.00,
  is_maintenance TINYINT NOT NULL DEFAULT 0,
  status TINYINT NOT NULL DEFAULT 1,
  created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY(id),
  UNIQUE KEY uq_game (game_code, vendor_code),
  KEY idx_game_code (game_code)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
CREATE TABLE IF NOT EXISTS financial_records (
  id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  user_id BIGINT UNSIGNED NOT NULL,
  record_no VARCHAR(80) NOT NULL,
  order_no VARCHAR(80) NOT NULL,
  vendor_code VARCHAR(80) DEFAULT '',
  type VARCHAR(80) NOT NULL,
  sub_type VARCHAR(80) DEFAULT '',
  amount DECIMAL(18,2) NOT NULL DEFAULT 0,
  back_amount DECIMAL(18,2) NOT NULL DEFAULT 0,
  remark VARCHAR(255) DEFAULT '',
  created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY(id),
  KEY idx_fin_user (user_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
CREATE TABLE IF NOT EXISTS withdraw_wallets (
  id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  user_id BIGINT UNSIGNED NOT NULL,
  wallet_type VARCHAR(80) NOT NULL,
  wallet_name VARCHAR(120) DEFAULT '',
  account_no VARCHAR(120) DEFAULT '',
  ifsc_code VARCHAR(80) DEFAULT '',
  mobile_no VARCHAR(80) DEFAULT '',
  wallet_data TEXT,
  status TINYINT NOT NULL DEFAULT 1,
  created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY(id),
  KEY idx_wallet_user (user_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
CREATE TABLE IF NOT EXISTS withdraw_requests (
  id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  user_id BIGINT UNSIGNED NOT NULL,
  amount DECIMAL(18,2) NOT NULL,
  method VARCHAR(80) DEFAULT 'BankCard',
  wallet_info TEXT,
  status ENUM('pending','approved','rejected') DEFAULT 'pending',
  admin_note VARCHAR(255) DEFAULT '',
  created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
  updated_at DATETIME DEFAULT NULL,
  PRIMARY KEY(id),
  KEY idx_withdraw_user (user_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
CREATE TABLE IF NOT EXISTS recharge_orders (
  id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  user_id BIGINT UNSIGNED NOT NULL,
  order_no VARCHAR(80) NOT NULL,
  recharge_category_id BIGINT DEFAULT NULL,
  channel_name VARCHAR(120) DEFAULT '',
  recharge_type VARCHAR(80) DEFAULT '',
  amount DECIMAL(18,2) NOT NULL DEFAULT 0,
  gift_amount DECIMAL(18,2) NOT NULL DEFAULT 0,
  pay_amount DECIMAL(18,2) NOT NULL DEFAULT 0,
  status ENUM('Wait','PendingReview','Payed','Cancel') NOT NULL DEFAULT 'Wait',
  raw_data LONGTEXT,
  created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
  updated_at DATETIME DEFAULT NULL,
  PRIMARY KEY(id),
  UNIQUE KEY uq_recharge_order (order_no),
  KEY idx_recharge_user (user_id),
  KEY idx_recharge_status (status)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
CREATE TABLE IF NOT EXISTS lottery_bets (
  id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  user_id BIGINT UNSIGNED NOT NULL,
  order_no VARCHAR(80) NOT NULL,
  game_code VARCHAR(120) NOT NULL,
  issue_number VARCHAR(80) NOT NULL,
  bet_content VARCHAR(255) NOT NULL,
  amount DECIMAL(18,2) NOT NULL,
  bet_multiple INT NOT NULL DEFAULT 1,
  real_amount DECIMAL(18,2) NOT NULL DEFAULT 0,
  fee DECIMAL(18,2) NOT NULL DEFAULT 0,
  premium VARCHAR(80) DEFAULT '',
  state TINYINT NOT NULL DEFAULT 2,
  win_lose_amount DECIMAL(18,2) NOT NULL DEFAULT 0,
  created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY(id),
  KEY idx_bet_user (user_id),
  KEY idx_bet_issue (issue_number)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
CREATE TABLE IF NOT EXISTS lottery_results (
  id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  game_code VARCHAR(120) NOT NULL,
  issue_number VARCHAR(80) NOT NULL,
  premium VARCHAR(120) NOT NULL,
  number VARCHAR(120) DEFAULT '',
  color VARCHAR(80) DEFAULT '',
  big_small VARCHAR(20) DEFAULT '',
  sum_value INT DEFAULT 0,
  open_time DATETIME DEFAULT NULL,
  created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY(id),
  UNIQUE KEY uq_result (game_code, issue_number),
  KEY idx_result_game (game_code)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
CREATE TABLE IF NOT EXISTS lottery_game_settings (
  id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  game_code VARCHAR(120) NOT NULL DEFAULT '*',
  win_rate DECIMAL(8,2) NOT NULL DEFAULT 45.00,
  force_mode ENUM('auto','win','lose') NOT NULL DEFAULT 'auto',
  force_result VARCHAR(120) DEFAULT '',
  fee_percent DECIMAL(8,2) NOT NULL DEFAULT 0.00,
  payout_number DECIMAL(10,2) NOT NULL DEFAULT 9.00,
  payout_color DECIMAL(10,2) NOT NULL DEFAULT 2.00,
  payout_violet DECIMAL(10,2) NOT NULL DEFAULT 4.50,
  payout_bigsmall DECIMAL(10,2) NOT NULL DEFAULT 2.00,
  payout_k3 DECIMAL(10,2) NOT NULL DEFAULT 2.00,
  payout_5d DECIMAL(10,2) NOT NULL DEFAULT 9.50,
  payout_moto DECIMAL(10,2) NOT NULL DEFAULT 9.50,
  immediate_settle TINYINT NOT NULL DEFAULT 0,
  updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY(id),
  UNIQUE KEY uq_lottery_setting_game (game_code)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
CREATE TABLE IF NOT EXISTS settings (
  setting_key VARCHAR(120) NOT NULL,
  setting_value LONGTEXT,
  PRIMARY KEY(setting_key)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
INSERT INTO users(id, tenant_user_id, username, mobile, email, password_hash, nickname, photo, real_name, balance, safe_box, vip_level, invite_code, role, status, created_at) VALUES
(117224,'60070000117224','919119098026','919119098026','','$2y$12$VmTn/Vy7ODeD72iWvtcdy.CV647wyVSFKM3IBkyjgRqDnxUZJq0aC','MemberNNGI5P66','1','Demo User',5000.00,0.00,1,'37L3UFN','user',1,NOW()),
(1,'60070000000001','admin','admin','','$2y$12$kE6YyGG8M1TWTnxaGCBfvuG.JVyNrdnehsz23jgoyT8n3mkUYPDMO','Administrator','1','Admin',100000.00,0.00,5,'ADMIN1','admin',1,NOW())
ON DUPLICATE KEY UPDATE password_hash=VALUES(password_hash), role=VALUES(role), status=1;
INSERT IGNORE INTO banners(id,name,icon_url,jump_type,jump_detail,display_target,sort,status) VALUES
(26,'Telegram Support','/img/6007/banner/104519387-32033-file_20260419104519376.webp',2,'https://t.me/GAME13L_BOT',1,9,1),
(25,'Telegram Channel','/img/6007/banner/104148119-32032-file_20260419104148118.webp',2,'https://t.me/GAME13L',1,8,1),
(24,'Daily Check-in','/img/6007/banner/035844411-31580-file_20260416155844410.webp',3,'20',1,6,1),
(23,'Recharge Reward','/img/6007/banner/035630931-31579-file_20260416155630929.webp',3,'19',1,5,1),
(22,'Week Card','/img/6007/banner/035453827-31578-file_20260416155453824.webp',3,'21',1,4,1),
(19,'Super Jackpot','/img/6007/banner/035023032-31574-file_20260416155023031.webp',3,'5',1,1,1);
INSERT IGNORE INTO game_categories(category_code,name_en,name_hi,sort,img,selected_img,status) VALUES
('Lottery','Lottery','लॉटरी',100,'/img/6007/gamecategory/013821461-35331-file_20260504133821456.webp','',1),
('Hot','Hot','हॉट',90,'/img/6007/gamecategory/015427496-35372-file_20260504135427491.webp','',1);
INSERT IGNORE INTO games(category_code,vendor_code,game_id,game_code,name,img,sort,rtp,is_maintenance,status) VALUES
('Lottery','ARLottery',1005,'WinGo_30S','WinGo 30sec','/img/6007/gamelogo/ARLottery/014807811-35339-file_20260504134807805.webp',44,98.0,0,1),
('Lottery','ARLottery',1001,'WinGo_1M','WinGo 1 Min','/img/6007/gamelogo/ARLottery/014823899-35341-file_20260504134823893.webp',43,98.0,0,1),
('Lottery','ARLottery',1003,'WinGo_3M','WinGo 3 Min','/img/6007/gamelogo/ARLottery/014841344-35343-file_20260504134841337.webp',42,98.0,0,1),
('Lottery','ARLottery',1005,'WinGo_5M','WinGo 5 Min','/img/6007/gamelogo/ARLottery/014856938-35345-file_20260504134856932.webp',41,98.0,0,1),
('Lottery','ARLottery',10501,'MotoRace_1M','Moto Racing','/img/6007/gamelogo/ARLottery/015329660-35371-file_20260504135329651.webp',36,98.0,0,1),
('Lottery','ARLottery',10101,'K3_1M','K3 1 Min','/img/6007/gamelogo/ARLottery/014920441-35347-file_20260504134920434.webp',34,98.0,0,1),
('Lottery','ARLottery',10103,'K3_3M','K3 3 Min','/img/6007/gamelogo/ARLottery/014939412-35349-file_20260504134939406.webp',33,98.0,0,1),
('Lottery','ARLottery',10201,'D5_1M','5D 1 Min','/img/6007/gamelogo/ARLottery/015032194-35355-file_20260504135032189.webp',24,98.0,0,1),
('Lottery','ARLottery',10203,'D5_3M','5D 3 Min','/img/6007/gamelogo/ARLottery/015045373-35357-file_20260504135045367.webp',23,98.0,0,1),
('Lottery','ARLottery',10301,'TrxWinGo_1M','Trx WinGo 1 Min','/img/6007/gamelogo/ARLottery/015204001-35363-file_20260504135203994.webp',14,98.0,0,1),
('Lottery','ARLottery',10303,'TrxWinGo_3M','Trx WinGo 3 Min','/img/6007/gamelogo/ARLottery/015219231-35365-file_20260504135219225.webp',13,98.0,0,1),
('Lottery','ARLottery',10305,'TrxWinGo_5M','Trx WinGo 5 Min','/img/6007/gamelogo/ARLottery/015231617-35367-file_20260504135231611.webp',12,98.0,0,1),
('Lottery','ARLottery',10105,'K3_5M','K3 5 Min','/img/6007/gamelogo/ARLottery/014957509-35351-file_20260504134957502.webp',32,98.0,0,1),
('Lottery','ARLottery',10205,'D5_5M','5D 5 Min','/img/6007/gamelogo/ARLottery/015058017-35359-file_20260504135058011.webp',22,98.0,0,1),
('Lottery','ARLottery',10503,'MotoRace_3M','Moto Racing 3 Min','/img/6007/gamelogo/ARLottery/015344546-35373-file_20260504135344541.webp',35,98.0,0,1),
('Lottery','ARLottery',10505,'MotoRace_5M','Moto Racing 5 Min','/img/6007/gamelogo/ARLottery/015356094-35375-file_20260504135356088.webp',34,98.0,0,1);
INSERT INTO lottery_game_settings(game_code, win_rate, force_mode, fee_percent, payout_number, payout_color, payout_violet, payout_bigsmall, payout_k3, payout_5d, payout_moto, immediate_settle) VALUES
('*',45.00,'auto',0.00,9.00,2.00,4.50,2.00,2.00,9.50,9.50,0),
('WinGo_30S',45.00,'auto',0.00,9.00,2.00,4.50,2.00,2.00,9.50,9.50,0),
('WinGo_1M',45.00,'auto',0.00,9.00,2.00,4.50,2.00,2.00,9.50,9.50,0),
('WinGo_3M',45.00,'auto',0.00,9.00,2.00,4.50,2.00,2.00,9.50,9.50,0),
('WinGo_5M',45.00,'auto',0.00,9.00,2.00,4.50,2.00,2.00,9.50,9.50,0),
('K3_1M',45.00,'auto',0.00,9.00,2.00,4.50,2.00,2.00,9.50,9.50,0),
('K3_3M',45.00,'auto',0.00,9.00,2.00,4.50,2.00,2.00,9.50,9.50,0),
('D5_1M',45.00,'auto',0.00,9.00,2.00,4.50,2.00,2.00,9.50,9.50,0),
('D5_3M',45.00,'auto',0.00,9.00,2.00,4.50,2.00,2.00,9.50,9.50,0),
('MotoRace_1M',45.00,'auto',0.00,9.00,2.00,4.50,2.00,2.00,9.50,9.50,0),
('TrxWinGo_1M',45.00,'auto',0.00,9.00,2.00,4.50,2.00,2.00,9.50,9.50,0),
('TrxWinGo_3M',45.00,'auto',0.00,9.00,2.00,4.50,2.00,2.00,9.50,9.50,0),
('TrxWinGo_5M',45.00,'auto',0.00,9.00,2.00,4.50,2.00,2.00,9.50,9.50,0),
('K3_5M',45.00,'auto',0.00,9.00,2.00,4.50,2.00,2.00,9.50,9.50,0),
('D5_5M',45.00,'auto',0.00,9.00,2.00,4.50,2.00,2.00,9.50,9.50,0),
('MotoRace_3M',45.00,'auto',0.00,9.00,2.00,4.50,2.00,2.00,9.50,9.50,0),
('MotoRace_5M',45.00,'auto',0.00,9.00,2.00,4.50,2.00,2.00,9.50,9.50,0)
ON DUPLICATE KEY UPDATE win_rate=VALUES(win_rate), force_mode=VALUES(force_mode), fee_percent=VALUES(fee_percent), payout_number=VALUES(payout_number), payout_color=VALUES(payout_color), payout_violet=VALUES(payout_violet), payout_bigsmall=VALUES(payout_bigsmall), payout_k3=VALUES(payout_k3), payout_5d=VALUES(payout_5d), payout_moto=VALUES(payout_moto), immediate_settle=VALUES(immediate_settle);
INSERT INTO settings(setting_key, setting_value) VALUES('auto_install_version','2026_05_16_v7_recharge_lottery_filter') ON DUPLICATE KEY UPDATE setting_value=VALUES(setting_value);
-- V13 migration: UTR, site settings, free-spin prizes
ALTER TABLE recharge_orders ADD COLUMN IF NOT EXISTS utr_submit_at DATETIME DEFAULT NULL;
ALTER TABLE recharge_orders ADD COLUMN IF NOT EXISTS user_submit_note VARCHAR(255) DEFAULT '';
ALTER TABLE recharge_orders ADD COLUMN IF NOT EXISTS payment_proof VARCHAR(500) DEFAULT '';
ALTER TABLE gift_codes ADD COLUMN IF NOT EXISTS title VARCHAR(190) DEFAULT 'Gift Code';
ALTER TABLE gift_codes ADD COLUMN IF NOT EXISTS per_user_limit INT NOT NULL DEFAULT 1;
ALTER TABLE gift_codes ADD COLUMN IF NOT EXISTS min_recharge DECIMAL(18,2) NOT NULL DEFAULT 0;
ALTER TABLE gift_codes ADD COLUMN IF NOT EXISTS admin_note VARCHAR(255) DEFAULT '';
ALTER TABLE invited_wheel_cycles ADD COLUMN IF NOT EXISTS turnover_required DECIMAL(18,2) NOT NULL DEFAULT 0;
ALTER TABLE invited_wheel_cycles ADD COLUMN IF NOT EXISTS turnover_completed DECIMAL(18,2) NOT NULL DEFAULT 0;
CREATE TABLE IF NOT EXISTS invited_wheel_free_prizes (
  id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  amount DECIMAL(18,2) NOT NULL DEFAULT 0,
  probability DECIMAL(10,4) NOT NULL DEFAULT 1,
  sort INT NOT NULL DEFAULT 0,
  status TINYINT NOT NULL DEFAULT 1,
  PRIMARY KEY(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
INSERT INTO invited_wheel_free_prizes(amount,probability,sort,status)
SELECT * FROM (SELECT 0.10,16,100,1 UNION ALL SELECT 0.20,15,95,1 UNION ALL SELECT 0.30,14,90,1 UNION ALL SELECT 0.50,13,85,1 UNION ALL SELECT 0.70,12,80,1 UNION ALL SELECT 1.00,10,75,1 UNION ALL SELECT 1.50,8,70,1 UNION ALL SELECT 2.00,7,65,1 UNION ALL SELECT 2.50,3,60,1 UNION ALL SELECT 3.00,2,55,1) AS x
WHERE NOT EXISTS (SELECT 1 FROM invited_wheel_free_prizes LIMIT 1);


CREATE TABLE IF NOT EXISTS recharge_wheel_records (
  id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  user_id BIGINT UNSIGNED NOT NULL,
  order_no VARCHAR(80) NOT NULL,
  recharge_wheel_type TINYINT NOT NULL DEFAULT 1,
  reward_type TINYINT NOT NULL DEFAULT 1,
  reward_amount DECIMAL(18,2) NOT NULL DEFAULT 0,
  created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY(id),
  UNIQUE KEY uq_recharge_wheel_order (order_no),
  KEY idx_recharge_wheel_user (user_id),
  KEY idx_recharge_wheel_type (recharge_wheel_type)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO settings(setting_key, setting_value) VALUES('recharge_wheel_config', '{"enabled":true,"rewardUpAmount":20000,"specialWheelUnlockAmount":50000,"noticeList":[{"message":"Me**H win ₹10"},{"message":"Get free spins after deposit"},{"message":"Complete deposit task to unlock wheel rewards"}],"wheels":{"1":{"name":"Silver Spin","label":"silver","remainSpinCount":0,"tasks":[[15000,2],[30000,2],[50000,2]],"rewards":[218,888,588,2888,128,388,688,188]},"2":{"name":"Gold Spin","label":"gold","remainSpinCount":0,"tasks":[[15000,2],[30000,2],[50000,2]],"rewards":[599,1888,8888,199,388,777,999,2999]},"3":{"name":"Diamond Spin","label":"diamond","remainSpinCount":0,"tasks":[[100000,3],[300000,3],[500000,3]],"rewards":[999,2999,9999,1999,399,667,888,4999]},"4":{"name":"Special Spin","label":"special","remainSpinCount":0,"tasks":[[500000,5],[1000000,5],[2000000,5]],"rewards":[1999,9999,20000,5000,888,1888,7777,2999]}}}') ON DUPLICATE KEY UPDATE setting_value=VALUES(setting_value);

-- V21 gift/license repair
CREATE TABLE IF NOT EXISTS gift_codes (id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT, code VARCHAR(80) NOT NULL, title VARCHAR(190) DEFAULT 'Gift Code', amount DECIMAL(18,2) NOT NULL DEFAULT 0, max_claim INT NOT NULL DEFAULT 1, claimed_count INT NOT NULL DEFAULT 0, per_user_limit INT NOT NULL DEFAULT 1, min_recharge DECIMAL(18,2) NOT NULL DEFAULT 0, expires_at DATETIME DEFAULT NULL, status TINYINT NOT NULL DEFAULT 1, admin_note VARCHAR(255) DEFAULT '', created_at DATETIME DEFAULT CURRENT_TIMESTAMP, PRIMARY KEY(id), UNIQUE KEY uq_gift_code(code)) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
CREATE TABLE IF NOT EXISTS gift_code_claims (id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT, gift_code_id BIGINT UNSIGNED NOT NULL, user_id BIGINT UNSIGNED NOT NULL, amount DECIMAL(18,2) NOT NULL DEFAULT 0, created_at DATETIME DEFAULT CURRENT_TIMESTAMP, PRIMARY KEY(id), UNIQUE KEY uq_gift_user(gift_code_id,user_id)) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


CREATE TABLE IF NOT EXISTS user_sessions (
  id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  user_id BIGINT UNSIGNED NOT NULL,
  session_token VARCHAR(80) NOT NULL,
  refresh_token VARCHAR(100) DEFAULT '',
  refresh_expires_at DATETIME DEFAULT NULL,
  device_id VARCHAR(120) DEFAULT '',
  ip VARCHAR(80) DEFAULT '',
  user_agent VARCHAR(255) DEFAULT '',
  is_active TINYINT NOT NULL DEFAULT 1,
  created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
  last_seen_at DATETIME DEFAULT NULL,
  PRIMARY KEY(id),
  UNIQUE KEY uq_session_token(session_token),
  KEY idx_user_session(user_id,is_active)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
CREATE TABLE IF NOT EXISTS admin_audit_logs (
  id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  admin_user_id BIGINT UNSIGNED NOT NULL DEFAULT 0,
  action VARCHAR(120) NOT NULL,
  target_type VARCHAR(80) DEFAULT '',
  target_id BIGINT UNSIGNED NOT NULL DEFAULT 0,
  ip VARCHAR(80) DEFAULT '',
  user_agent VARCHAR(255) DEFAULT '',
  data_json TEXT,
  created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY(id),
  KEY idx_action_created(action,created_at),
  KEY idx_target(target_type,target_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
