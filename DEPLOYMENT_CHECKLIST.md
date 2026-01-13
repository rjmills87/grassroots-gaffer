# Production Deployment Checklist

Use this checklist when deploying Grassroots Gaffer to production. Follow the detailed instructions in `DEPLOYMENT_GUIDE.md`.

## Pre-Deployment

- [ ] All local tests pass (`composer test` or `php artisan test`)
- [ ] UI/UX testing completed (see `TESTING_GUIDE.md`)
- [ ] Code reviewed and ready for production
- [ ] Database backup strategy planned
- [ ] Domain name registered and DNS configured
- [ ] SSL certificate obtained (Let's Encrypt recommended)

## Server Setup

- [ ] Server provisioned with PHP 8.2+
- [ ] Node.js 22+ installed
- [ ] Composer installed
- [ ] Database server set up (MySQL/MariaDB recommended)
- [ ] Web server configured (Nginx or Apache)
- [ ] Firewall configured
- [ ] SSH access configured securely

## Application Deployment

- [ ] Repository cloned to server
- [ ] PHP dependencies installed (`composer install --optimize-autoloader --no-dev`)
- [ ] Node dependencies installed (`npm ci`)
- [ ] Production assets built (`npm run build`)
- [ ] `.env` file created and configured
- [ ] Application key generated (`php artisan key:generate`)
- [ ] Database migrations run (`php artisan migrate --force`)
- [ ] Application optimized:
  - [ ] Config cached (`php artisan config:cache`)
  - [ ] Routes cached (`php artisan route:cache`)
  - [ ] Views cached (`php artisan view:cache`)
  - [ ] Ziggy routes generated (`php artisan ziggy:generate`)
- [ ] File permissions set correctly
- [ ] Storage directories writable

## Configuration

- [ ] Environment variables set:
  - [ ] `APP_ENV=production`
  - [ ] `APP_DEBUG=false`
  - [ ] `APP_URL` set to production domain
  - [ ] Database credentials configured
  - [ ] Mail service configured and tested
  - [ ] File storage configured
- [ ] Web server virtual host configured
- [ ] SSL certificate installed and working
- [ ] Queue worker configured (Supervisor or similar)

## Email Configuration

- [ ] Mail service provider chosen (SMTP/Mailgun/Postmark)
- [ ] Mail credentials configured in `.env`
- [ ] Test email sent and received
- [ ] Welcome emails tested
- [ ] Password reset emails tested
- [ ] Event reminder emails tested

## Post-Deployment Verification

- [ ] Application loads at production URL
- [ ] SSL certificate working (HTTPS)
- [ ] User registration works
- [ ] User login works
- [ ] Email verification works
- [ ] Team creation works
- [ ] Player addition works
- [ ] Welcome emails sent correctly
- [ ] Event creation works
- [ ] Availability tracking works
- [ ] Team messaging works
- [ ] Message edit/delete works
- [ ] Reminder emails work
- [ ] Mobile responsiveness verified
- [ ] Error pages display correctly (404, 500, etc.)

## Monitoring & Maintenance

- [ ] Error logging configured
- [ ] Log rotation set up
- [ ] Database backup automated
- [ ] Uptime monitoring configured
- [ ] Queue worker monitoring configured
- [ ] Performance monitoring set up (optional)

## Security

- [ ] `APP_DEBUG=false` verified
- [ ] Strong database passwords set
- [ ] `.env` file not web-accessible
- [ ] File permissions secure
- [ ] Firewall rules configured
- [ ] Regular security updates scheduled
- [ ] Sensitive data not exposed in errors

## Documentation

- [ ] `USER_GUIDE.md` shared with test users
- [ ] Support contact information provided
- [ ] Deployment documentation updated
- [ ] Known issues documented

## Launch

- [ ] Final smoke test completed
- [ ] Test users notified
- [ ] Support channels ready
- [ ] Monitoring active
- [ ] **DEPLOYED! 🚀**

## Post-Launch

- [ ] Monitor error logs for first 24 hours
- [ ] Gather initial user feedback
- [ ] Address any critical issues
- [ ] Plan for improvements based on feedback

---

**Notes:**
- Keep this checklist updated as deployment process evolves
- Document any server-specific steps needed
- Save deployment timestamps and versions

**Deployment Date:** _______________
**Deployed By:** _______________
**Version:** _______________
