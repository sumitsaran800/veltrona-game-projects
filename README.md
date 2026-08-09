# All 4 Game Projects & Database Dumps

This repository contains the complete production-ready source code, database dumps, configuration details, and cron job commands for all 4 gaming applications.

---

## 📁 Repository Structure

```
├── TIRNGA_VUE_JS/     # Tiranga Vue Source Code
├── 92go_vue/          # 92go Vue Source Code
├── 89club/            # 89 Club Source Code
├── 13l_game/          # 13L Game Source Code
├── databases/         # Database SQL Dump Files
│   ├── onorc_tirngatirnga.sql
│   ├── onorc_92go.sql
│   ├── onorc_89club.sql
│   └── onorc_13l.sql
└── README.md
```

---

## 🌐 Subdomain & Database Configurations

### 1. TIRNGA_VUE_JS
- **Production URL:** `https://tirnga.veltronaerp.in`
- **Database Name:** `onorc_tirngatirnga`
- **Database User:** `onorc_tirngatirnga`
- **Database Password:** `onorc_tirngatirnga`
- **SQL Dump:** `databases/onorc_tirngatirnga.sql`

### 2. 92go vue
- **Production URL:** `https://92go.veltronaerp.in`
- **Database Name:** `onorc_92go`
- **Database User:** `onorc_92go`
- **Database Password:** `onorc_92go`
- **SQL Dump:** `databases/onorc_92go.sql`

### 3. 89 Club
- **Production URL:** `https://89club.veltronaerp.in`
- **Database Name:** `onorc_89club`
- **Database User:** `onorc_89club`
- **Database Password:** `onorc_89club`
- **SQL Dump:** `databases/onorc_89club.sql`

### 4. 13L Game
- **Production URL:** `https://13l.veltronaerp.in`
- **Database Name:** `onorc_13l`
- **Database User:** `onorc_13l`
- **Database Password:** `onorc_13l`
- **SQL Dump:** `databases/onorc_13l.sql`

---

## 🔑 Login Credentials

### App User Login:
- **Mobile Number:** `9999999999` (or `6289959895`)
- **Password:** `123456`

### Admin Panel Logins:
- **Tiranga Vue:** `https://tirnga.veltronaerp.in/dkh/index.php` -> User: `admin` | Pass: `123456`
- **92go Vue:** `https://92go.veltronaerp.in/main/index.php` -> User: `admin` | Pass: `123456`
- **89 Club:** `https://89club.veltronaerp.in/main/index.php` -> User: `admin` | Pass: `123456`
- **13L Game:** `https://13l.veltronaerp.in/admin/index.php` -> User: `admin` | Pass: `123456`

---

## ⏱️ cPanel Cron Job Commands

### 🟢 Tiranga Vue (`https://tirnga.veltronaerp.in`)
```bash
* * * * * curl -s "https://tirnga.veltronaerp.in/niyamitakelasa.php" >/dev/null 2>&1
* * * * * curl -s "https://tirnga.veltronaerp.in/niyamitakelasa_drei.php" >/dev/null 2>&1
* * * * * curl -s "https://tirnga.veltronaerp.in/niyamitakelasa_funf.php" >/dev/null 2>&1
* * * * * curl -s "https://tirnga.veltronaerp.in/niyamitakelasa_zehn.php" >/dev/null 2>&1
* * * * * curl -s "https://tirnga.veltronaerp.in/ktrx.php" >/dev/null 2>&1
0 0 * * * curl -s "https://tirnga.veltronaerp.in/dailysalary.php" >/dev/null 2>&1
0 0 * * * curl -s "https://tirnga.veltronaerp.in/commintioncron.php" >/dev/null 2>&1
```

### 🟡 92go Vue (`https://92go.veltronaerp.in`)
```bash
* * * * * curl -s "https://92go.veltronaerp.in/niyamitakelasa.php" >/dev/null 2>&1
* * * * * curl -s "https://92go.veltronaerp.in/niyamitakelasa_drei.php" >/dev/null 2>&1
* * * * * curl -s "https://92go.veltronaerp.in/niyamitakelasa_funf.php" >/dev/null 2>&1
* * * * * curl -s "https://92go.veltronaerp.in/niyamitakelasa_zehn.php" >/dev/null 2>&1
0 0 * * * curl -s "https://92go.veltronaerp.in/dailysalary.php" >/dev/null 2>&1
0 0 * * * curl -s "https://92go.veltronaerp.in/commintioncron.php" >/dev/null 2>&1
```

### 🔴 89 Club (`https://89club.veltronaerp.in`)
```bash
* * * * * curl -s "https://89club.veltronaerp.in/niyamitakelasa.php" >/dev/null 2>&1
* * * * * curl -s "https://89club.veltronaerp.in/niyamitakelasa_drei.php" >/dev/null 2>&1
* * * * * curl -s "https://89club.veltronaerp.in/niyamitakelasa_funf.php" >/dev/null 2>&1
* * * * * curl -s "https://89club.veltronaerp.in/niyamitakelasa_zehn.php" >/dev/null 2>&1
0 0 * * * curl -s "https://89club.veltronaerp.in/dailysalary.php" >/dev/null 2>&1
0 0 * * * curl -s "https://89club.veltronaerp.in/commintioncron.php" >/dev/null 2>&1
```

### 🔵 13L Game (`https://13l.veltronaerp.in`)
```bash
* * * * * curl -s "https://13l.veltronaerp.in/api/cron/cron_wingo.php" >/dev/null 2>&1
* * * * * curl -s "https://13l.veltronaerp.in/api/cron/cron_trx.php" >/dev/null 2>&1
* * * * * curl -s "https://13l.veltronaerp.in/api/cron/cron_5d.php" >/dev/null 2>&1
* * * * * curl -s "https://13l.veltronaerp.in/api/cron/cron_k3.php" >/dev/null 2>&1
```
