# Squadra365 - UI/UX Testing Guide

This guide provides step-by-step instructions for testing both coach and guardian workflows to ensure the MVP is ready for deployment.

## Prerequisites

- Local development environment running
- Two email accounts (one for coach, one for guardian)
- Access to email inboxes for testing welcome emails and password resets
- Mobile device or browser dev tools for mobile responsiveness testing

---

## Coach Workflow Testing

### 1. Create a Team

**Steps:**
1. Log in as a coach user
2. Navigate to Dashboard
3. Click "Create Team" button
4. Fill in the form:
   - Team name (e.g., "U12 Eagles")
   - Age group (select from dropdown)
   - Team badge (optional - upload an image)
5. Submit the form

**Expected Results:**
- ✅ Success toast notification appears: "Team created successfully"
- ✅ Team appears on dashboard
- ✅ Team badge displays correctly (or placeholder if none uploaded)
- ✅ Can click on team to view team details page

**Check For:**
- Form validation (try submitting empty form)
- Error messages display correctly
- Team badge upload works
- Mobile responsiveness of form

---

### 2. Add Players to Roster

**Steps:**
1. Navigate to team detail page
2. Click "Add Player" button
3. Fill in player form:
   - Player name
   - Guardian name
   - Guardian email (use a real email you can access)
   - Guardian phone
   - Squad number (optional)
   - Position (optional)
4. Submit form

**Expected Results:**
- ✅ Success toast: "Player has been successfully added to the team"
- ✅ Player appears in squad list table
- ✅ Guardian receives welcome email
- ✅ New player is automatically added to all future events

**Check For:**
- Form validation (try invalid email, duplicate squad number)
- Email is sent to guardian
- Welcome email contains correct team and player information
- Player appears in table with correct information
- Mobile view of table is readable

---

### 3. Schedule an Event

**Steps:**
1. On team detail page, click "Create Event" button
2. Fill in event form:
   - Event type (Match or Practice)
   - Date and time
   - Location
   - Details (optional)
3. Submit form

**Expected Results:**
- ✅ Success toast notification appears
- ✅ Event appears in "Upcoming Events" list
- ✅ Event shows on dashboard
- ✅ All team players are automatically added to event

**Check For:**
- Date picker works correctly
- Time selection works
- Event displays with correct formatting
- Events are sorted by date (upcoming first)
- Mobile view of event list

---

### 4. Send a Team Message

**Steps:**
1. On team detail page, scroll to Messages section
2. Click "Create Message" button
3. Type a message (max 255 characters)
4. Submit

**Expected Results:**
- ✅ Success toast: "Your message has been created successfully"
- ✅ Message appears in message list
- ✅ Message shows sender name and timestamp
- ✅ Message is visible to all team members

**Check For:**
- Character limit validation (try >255 characters)
- Message formatting displays correctly
- Timestamp is accurate
- Mobile view of messages

---

### 5. View Player Availability

**Steps:**
1. Navigate to an event detail page (click on event from team page or dashboard)
2. View the list of players
3. Check availability status for each player

**Expected Results:**
- ✅ All team players are listed
- ✅ Availability status is clearly displayed (Attending/Unavailable/No Response)
- ✅ Visual indicators (colors) show status clearly
- ✅ Coach can see who hasn't responded

**Check For:**
- Clear visual distinction between statuses
- List is easy to read
- Mobile view is usable
- Status updates correctly when guardians respond

---

### 6. Send Reminders to Non-Responders

**Steps:**
1. On event detail page, identify players who haven't responded
2. Click "Send Reminder" button
3. Check guardian email inbox

**Expected Results:**
- ✅ Reminder email is sent only to guardians who haven't responded
- ✅ Email contains event details and link to respond
- ✅ Success feedback is provided

**Check For:**
- Only non-responders receive reminders
- Email contains correct event information
- Link in email works correctly
- Button is only visible to coaches

---

### 7. Edit and Delete Messages

**Steps:**
1. On team detail page, find a message you created
2. Click "Edit Message" button
3. Modify the message text
4. Click "Save Message"
5. Try deleting a message

**Expected Results:**
- ✅ Edit mode shows textarea with current message
- ✅ Success toast: "Your message has been saved successfully"
- ✅ Updated message displays correctly
- ✅ Delete shows confirmation
- ✅ Success toast: "Your message has been deleted successfully"
- ✅ Message is removed from list

**Check For:**
- Only message creator can edit/delete
- Edit and delete buttons only show for own messages
- Confirmation prevents accidental deletion
- UI updates immediately after save/delete

---

### 8. Delete a Team

**Steps:**
1. On team detail page, scroll to bottom
2. Click "Delete Team" button
3. Confirm deletion

**Expected Results:**
- ✅ Confirmation dialog appears
- ✅ Success toast: "Team: [name] has been deleted successfully"
- ✅ Redirected to dashboard
- ✅ Team no longer appears on dashboard

**Check For:**
- Confirmation prevents accidental deletion
- All related data is handled correctly
- Redirect works properly

---

## Guardian Workflow Testing

### 1. Receive Welcome Email

**Steps:**
1. As a coach, add a player with a guardian email you control
2. Check guardian email inbox

**Expected Results:**
- ✅ Welcome email is received
- ✅ Email contains team name and player name
- ✅ Email contains password reset link
- ✅ Email is well-formatted and professional

**Check For:**
- Email arrives promptly
- Links work correctly
- Email content is clear and helpful

---

### 2. Set Up Password via Reset Link

**Steps:**
1. Click password reset link in welcome email
2. Enter new password
3. Confirm password
4. Submit

**Expected Results:**
- ✅ Password reset page loads correctly
- ✅ Password requirements are clear
- ✅ Success message appears
- ✅ Can log in with new password

**Check For:**
- Password validation works
- Link expires after use (security)
- Clear error messages
- Mobile-friendly form

---

### 3. Log In to Dashboard

**Steps:**
1. Navigate to login page
2. Enter guardian email and password
3. Click "Log In"

**Expected Results:**
- ✅ Successful login
- ✅ Redirected to dashboard
- ✅ Dashboard shows guardian's teams
- ✅ Dashboard shows upcoming events for their players

**Check For:**
- Error handling for wrong credentials
- Remember me functionality (if implemented)
- Dashboard loads correctly
- Mobile view is usable

---

### 4. View Team and Upcoming Events

**Steps:**
1. Log in as guardian
2. View dashboard
3. Click on a team to view details

**Expected Results:**
- ✅ Dashboard shows all teams where guardian has players
- ✅ Upcoming events are displayed
- ✅ Team detail page shows:
   - Team name and badge
   - Squad list (with their player highlighted or visible)
   - Upcoming events
   - Team messages

**Check For:**
- Only relevant teams are shown
- Events are sorted correctly
- Player information is accurate
- Mobile view is readable
- Navigation is intuitive

---

### 5. Respond to Event Availability

**Steps:**
1. Click on an upcoming event (from dashboard or team page)
2. View event details
3. Find your player in the list
4. Click "Attending" or "Unavailable" button

**Expected Results:**
- ✅ Button state changes to show selection
- ✅ Visual feedback (color change) indicates selection
- ✅ Response is saved immediately
- ✅ Coach can see the response

**Check For:**
- Buttons are clearly labeled
- Visual feedback is immediate
- Can change response if needed
- Mobile buttons are large enough to tap easily
- Only guardian's own players show response buttons

---

### 6. Receive Reminder Notification

**Steps:**
1. As a coach, send a reminder for an event
2. Check guardian email inbox (for a guardian who hasn't responded)

**Expected Results:**
- ✅ Reminder email is received
- ✅ Email contains event details
- ✅ Link to respond is included and works
- ✅ Email is clear and actionable

**Check For:**
- Email arrives promptly
- Link works correctly
- Email content is helpful
- Only non-responders receive reminders

---

## Cross-Cutting UI/UX Checks

### Mobile Responsiveness

Test all pages on mobile device or browser dev tools (mobile view):

- ✅ Dashboard displays correctly on small screens
- ✅ Forms are usable on mobile
- ✅ Tables are readable (may need horizontal scroll)
- ✅ Buttons are large enough to tap easily
- ✅ Navigation is accessible
- ✅ Text is readable without zooming

### Error Handling

Test error scenarios:

- ✅ Form validation errors display clearly
- ✅ Network errors are handled gracefully
- ✅ 404 pages are user-friendly
- ✅ Unauthorized access shows appropriate message
- ✅ Error messages are helpful, not technical

### Feedback Messages

Check all user actions:

- ✅ Success toasts appear for all successful actions
- ✅ Error messages are clear and actionable
- ✅ Loading states show during form submissions
- ✅ Confirmation dialogs prevent accidental actions

### Navigation

Test navigation flow:

- ✅ Breadcrumbs work correctly
- ✅ Back button works as expected
- ✅ Links are clearly identifiable
- ✅ Can easily navigate between sections

### Consistency

Check design consistency:

- ✅ Colors are consistent throughout
- ✅ Fonts and sizes are consistent
- ✅ Spacing is consistent
- ✅ Button styles are consistent
- ✅ Form styles are consistent

---

## Testing Checklist Summary

### Coach Workflow
- [ ] Create a team
- [ ] Add players to roster
- [ ] Schedule an event
- [ ] Send a team message
- [ ] View player availability
- [ ] Send reminders to non-responders
- [ ] Edit messages
- [ ] Delete messages
- [ ] Delete a team

### Guardian Workflow
- [ ] Receive welcome email
- [ ] Set up password via reset link
- [ ] Log in to dashboard
- [ ] View team and upcoming events
- [ ] Respond to event availability
- [ ] Receive reminder notification

### UI/UX Quality
- [ ] Mobile responsiveness verified
- [ ] Error handling tested
- [ ] Feedback messages verified
- [ ] Navigation tested
- [ ] Design consistency checked

---

## Reporting Issues

When you find issues during testing:

1. Note the specific workflow step
2. Describe what happened vs. what was expected
3. Include screenshots if helpful
4. Note browser/device used
5. Check browser console for errors

---

## Next Steps After Testing

Once testing is complete:

1. Fix any critical issues found
2. Document any known limitations
3. Update UI/UX review checklist
4. Proceed to production deployment
