# Hostinger Shared Hosting Deployment (PHP + MySQL)

This project now uses:
- React frontend build (existing frontend unchanged)
- PHP API files (`/api/*.php`)
- MySQL database

## 1) Build frontend
Run locally:
```bash
cd frontend
npm install
npm run build
```

## 2) Prepare backend config
- Copy `backend/api/config.php.example` to `backend/api/config.php`
- Set your Hostinger MySQL credentials in `backend/api/config.php`

## 3) Create database tables
- Open Hostinger phpMyAdmin
- Run SQL from `backend/database/schema.sql`

## 4) Upload to Hostinger `public_html`
Upload these contents:
- `frontend/build/*` -> `public_html/`
- `backend/api/*` -> `public_html/api/`

Keep this structure in production:
- `public_html/index.html`
- `public_html/static/...`
- `public_html/api/*.php`
- `public_html/api/auth/*.php`
- `public_html/api/includes/*.php`
- `public_html/api/uploads/`

## 5) Verify
- Website: `https://yourdomain.com`
- API health: `https://yourdomain.com/api/health.php`
- Login API: `https://yourdomain.com/api/auth/login.php`

## Notes
- All API SQL queries use prepared statements (PDO).
- Admin auth uses PHP sessions.
- Upload endpoint stores files in `api/uploads` and returns `/api/uploads/<filename>`.
