---
trigger: manual
---

# UI/UX Review Checklist

## 🎯 Goal

Test both coach and guardian workflows to identify and fix usability issues before deployment.

---

## 🔍 Issues Found

### High Priority

- [x] **Message Management**: Coaches should be able to edit and delete their own messages

### Medium Priority

- [ ] **Messaging Conversations**: Add ability for guardians to reply to messages (currently one-way announcements only)

### Low Priority

---

## ✅ Improvements Completed

- [x] Removed role selector from registration (coaches only)
- [x] Updated registration button text to "Coach Registration"
- [x] Fixed bug: New players now auto-added to future events
- [x] Add Delete a team functionality for coaches

---

## 📋 Testing Workflows

### Coach Workflow

- [ ] Create a team
- [ ] Add players to roster
- [ ] Schedule an event
- [ ] Send a team message
- [ ] View player availability
- [ ] Send reminders to non-responders

### Guardian Workflow

- [ ] Receive welcome email
- [ ] Set up password via reset link
- [ ] Log in to dashboard
- [ ] View team and upcoming events
- [ ] Respond to event availability
- [ ] Receive reminder notification

---

## 🎨 What to Look For

- **Confusing UI elements** - Is anything unclear or hard to find?
- **Missing feedback messages** - Do actions confirm success/failure?
- **Mobile responsiveness** - Does it work well on smaller screens?
- **Inconsistent styling** - Are colors, fonts, spacing consistent?
- **Navigation problems** - Can you easily get where you need to go?
- **Missing features** - Should users be able to do something they currently can't?
