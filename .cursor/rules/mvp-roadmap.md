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

## 🚀 Post-MVP Enhancements

- [ ] **Team Join Code System:** Implement a feature where each team is assigned a unique, random join code. A coach can share this code with parents, who can then use it to have their child join the team roster automatically. This removes the need for the coach to manually invite every parent.
