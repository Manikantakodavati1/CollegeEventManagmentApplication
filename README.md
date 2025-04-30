# CollegeEventManagmentApplication
College life is filled with various events such as technical fests, cultural programs, seminars, sports competitions, and workshops that contribute to the overall development of students. Managing these events manually often leads to confusion, miscommunication, and inefficiencies. Event organizers must deal with registration forms, participant tracking, announcements, scheduling conflicts, and report generation — all of which are time-consuming and prone to errors.

The CampusEvents is a digital platform developed to simplify and streamline the entire process of organizing and managing college events. This web-based application provides a centralized system that allows students to view events, register online, and receive updates, while administrators and faculty can create, manage, and analyse events with ease.
This system aims to:
•	Eliminate the need for paperwork and manual registration
•	Provide real-time access to event information
•	Improve coordination between different stakeholders (students, faculty, and organizers)
•	Automate the reporting and data management processes

With the rapid advancement of technology and the increasing reliance on digital tools in academic institutions, this system plays a crucial role in enhancing the overall efficiency and effectiveness of event management within a college environment.
Key Features of the System:
•	Secure login for students, faculty, and administrators
•	Event listing with descriptions, dates, and organizers
•	Online registration for participants
•	Database storage for historical records and reports

The College Event Management System ensures a smooth, transparent, and digitally driven approach to handling events, contributing to better student engagement, time management, and organizational success.

objectives
Main Objective: Develop a centralized web application for managing college events.
Sub-objectives:
•	Allow students to register and view event details
•	Enable faculty to manage events and approve requests
•	Provide real-time notifications and updates
•	Automate report generation for attendance and winners
•	Secure authentication for admin, faculty, and student roles

Implementation
1. User Authentication
Integrated using Supabase Auth.
Roles: Admin, Student, Event Coordinator.
Secure login/logout and session management.

2. Event Creation & Management
Admins and coordinators can create, update, and delete events.
Fields include Event Name, Description, Date, Time, Venue, Capacity.
Data stored and managed using Supabase tables.

3. Event Registration
Students can view available events and register.
Registration data is stored in Supabase with user-event linkage.

4. Event Listings 
Displays all upcoming and past events.
Filters by category, date, or coordinator.
Real-time updates using Supabase's reactive capabilities.


5. Notifications / Announcements
Event updates or reminders displayed to registered users.
Optional: Email or in-app alerts using Supabase functions


