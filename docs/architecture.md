# Architecture

Application Flow

Route
↓
Controller
↓
Service
↓
Model

Controller:
Receive Request
Validate Request
Call Service
Return Response

Service:
Business Logic
Database Transaction
Call Model

Model:
Database Interaction
Avoid putting business logic inside Model.
Keep Controller thin.
Keep Service reusable.
