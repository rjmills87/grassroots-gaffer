---
trigger: always_on
---

# ⚽ The Grassroots Coach App (Grassroots Gaffer): MVP Roadmap ⚽

## Our Mission

To build an ad-free, intuitive mobile app for grassroots football coaches that makes team management a joy, not a chore.

---

## 🎯 MVP Goal

Create a simple, functional app that solves the core problem of **team communication and availability tracking** in a way that is better than existing solutions.

**Timeline:** 3 Weeks

---

## 💳 Product Tiers (Free vs Pro)

To align product delivery with commercial goals, MVP should ship with two clear tiers:

### Free Tier (Core Team Admin)

- Team creation and basic team profile setup
- Roster management (players + guardian contact details)
- Event scheduling (matches/training with date, time, location)
- Availability tracking (guardian responses for events)
- Basic team announcements/messages
- Secure, ad-free experience

### Pro Tier (Coach Efficiency)

- Automated reminder sends to non-responding guardians
- Priority support / faster issue response
- Advanced communication and automation features (phased post-MVP)

### Tiering Principles

- Free tier must deliver clear value to solo coaches and small teams.
- Pro tier must save coaches time, not just add cosmetic features.
- Existing free users should never lose access to core historical data.

---

## 🗓️ Sprint 1: The Foundation (Week 1)

**Goal:** Get the core user and team infrastructure in place.

### Key Tasks:

- [x] Set up the Laravel, Inertia, and Vue project using a starter kit.
- [x] Implement **User Authentication** (login, registration, logout).
- [x] Implement a basic role system (e.g., Coach, Parent/Guardian, Player).
- [x] Design and build the basic **Coach Dashboard** view.
- [x] Add the functionality for a coach to **Create a Team**.
- [x] Build the **Roster Management** screen, allowing a coach to add player/parent emails to the team.

### Stretch Goal:

- [x] Send an initial "Welcome to the Team" email to parents added to the roster.

---

## 🗓️ Sprint 2: Core Communication (Week 2)

**Goal:** Implement the primary features that will deliver the most value to the coach and parents.

### Key Tasks:

- [x] Build the **Event Scheduling** form (match/practice with date, time, and location).
- [x] Display a clean, organized **Upcoming Events** list on the team dashboard.
- [x] Implement the **Availability Tracking** system, with a link in an event notification that allows parents to easily respond (Yes/No).
- [x] On the coach's side, show a clear, visual summary of player availability for each event.
- [x] Create a simple **Team Messaging** feature for coaches to send one-way announcements.

### Stretch Goal:

- [x] Add a feature that allows a coach to quickly send a reminder message to parents who haven't responded yet.

---

## 🗓️ Sprint 3: Polish and Launch (Week 3)

**Goal:** Refine the user experience and prepare the app for its first users.

### Key Tasks:

- [x] Review and improve the UI/UX for all features, ensuring it is intuitive and mobile-friendly. (Testing guide created: `TESTING_GUIDE.md`)
- [x] Add a **Parent Dashboard** that shows their team(s) and upcoming events.
- [x] Add basic **Error Handling** and form validation.
- [x] Set up the production environment for deployment. (Deployment guide created: `DEPLOYMENT_GUIDE.md`)
- [ ] Deploy the MVP to a web server. (Ready for deployment - see `DEPLOYMENT_CHECKLIST.md`)
- [x] Prepare a welcome message or a short guide for the first test users. (`USER_GUIDE.md` created)

### Stretch Goal:

- [x] Implement a simple landing page that explains the app's mission and value proposition.

---

## ✅ Mindset

- **Focus on Functionality:** A working feature is better than a perfect but unfinished one.
- **Embrace Feedback:** The goal is to get this into the hands of real coaches to learn what they actually need.
- **Keep it Simple:** If a feature isn't listed here, don't build it yet.

---

## 🚀 Post-MVP Execution Roadmap

### Phase 4: Monetization Foundations

**Goal:** Move from MVP utility to a sustainable product without degrading the free core experience.

#### Deliverables:

- [ ] **Free vs Pro Entitlements:** Define and enforce feature gates so free and pro access is explicit and testable.
- [ ] **Billing Foundations:** Implement subscription and payment collection rails for coach/club plans.
- [ ] **Billing Operations:** Add webhook/event handling for billing state changes (active, past due, cancelled).
- [ ] **Payment Safety:** Add audit-friendly billing logs and failure/retry flows for payment-related actions.

### Phase 5: Competitive Parity Features

**Goal:** Close the most important product gaps with tools coaches use weekly.

#### Deliverables:

- [ ] **Recurring Events:** Support repeat match/training schedules with easy edit controls.
- [ ] **Communication Upgrade:** Expand from one-way announcements toward structured replies/threads.
- [ ] **Calendar Integration:** Add calendar export/sync milestones (ICS first, provider sync later).
- [ ] **Notification Reliability:** Improve reminder delivery, visibility, and recovery for failed sends.
- [ ] **Team Join Code System:** Add unique join codes so guardians can join teams without manual invites.
- [ ] **Pro Automation Pack:** Expand pro-only automation such as templates, scheduled nudges, and attendance insights.

### Phase 6: Retention and Optimization

**Goal:** Improve long-term team retention and prove value of paid features.

#### Deliverables:

- [ ] **Coach Workflow Speed:** Reduce steps/time to create event, notify team, and collect responses.
- [ ] **Activation Improvements:** Improve first-session flow from signup to first team and first event.
- [ ] **Pro Value Iteration:** Prioritize paid features that save coaches measurable weekly admin time.

## 📈 Metrics and Success Criteria

- **Activation:** % of new coaches who create a team and first event within 24 hours.
- **Engagement:** Event RSVP response rate and weekly active teams.
- **Reminder Effectiveness:** % of no-response guardians who respond after a reminder.
- **Monetization:** Free-to-pro conversion rate once billing is enabled.
- **Monetization Health:** Pro churn rate and retained paid teams after month one.

## 🧭 Prioritization Rule

If two features compete for the same sprint slot, prioritize the one that reduces weekly coach admin time the most.
