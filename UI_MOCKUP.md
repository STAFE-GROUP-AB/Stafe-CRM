# Stalled Customers Feature - UI Mockup

## Dashboard Alert Card (when stalled customers exist)
```
┌─────────────────────────────────────────────────────────────────────────┐
│ ⚠️  12 Stalled Customers                                                 │
│ These customers haven't been contacted in the last 30 days              │
│                                         [View Stalled Customers] │
└─────────────────────────────────────────────────────────────────────────┘
```

## Main Navigation Menu
```
┌─────────────────────────────────────────────────────────────────────────┐
│ 🏠 Dashboard  👥 Contacts  🏢 Companies  💼 Deals  📋 Tasks  ⚠️ Stalled Customers │
└─────────────────────────────────────────────────────────────────────────┘
```

## Stalled Customers Page Layout
```
┌─────────────────────────────────────────────────────────────────────────┐
│ Stalled Customers                                                        │
│ Customers that haven't been contacted recently                           │
│                                                                         │
│ ┌─────────────┐ ┌─────────────┐ ┌─────────────┐                        │
│ │ Total: 12   │ │ Never: 3    │ │ Avg: 45 days│                        │
│ └─────────────┘ └─────────────┘ └─────────────┘                        │
│                                                                         │
│ ┌─────────────────────────────────────────────────────────────────────┐ │
│ │ Search: [__________] Rep: [All ▼] Period: [30 days ▼] [Refresh] │ │
│ └─────────────────────────────────────────────────────────────────────┘ │
│                                                                         │
│ Stalled by Sales Rep:                                                   │
│ ┌─────────────┐ ┌─────────────┐ ┌─────────────┐                        │
│ │ John S. (4) │ │ Mary J. (5) │ │ Bob T. (3)  │                        │
│ └─────────────┘ └─────────────┘ └─────────────┘                        │
│                                                                         │
│ ┌─────────────────────────────────────────────────────────────────────┐ │
│ │ 👤 John Doe                          John Smith                      │ │
│ │    john@example.com • ABC Corp       Last: 45 days ago              │ │
│ │                                      [Mark Contacted] [View]        │ │
│ │─────────────────────────────────────────────────────────────────────│ │
│ │ 👤 Jane Smith                        Mary Johnson                    │ │
│ │    jane@example.com • XYZ Inc        Last: Never contacted          │ │
│ │                                      [Mark Contacted] [View]        │ │
│ │─────────────────────────────────────────────────────────────────────│ │
│ │ 👤 Bob Wilson                        Bob Thompson                    │ │
│ │    bob@example.com • Tech Co         Last: 60 days ago              │ │
│ │                                      [Mark Contacted] [View]        │ │
│ └─────────────────────────────────────────────────────────────────────┘ │
│                                                                         │
│ ← Previous | 1 2 3 | Next →                                            │
└─────────────────────────────────────────────────────────────────────────┘
```

## Key UI Elements

### 1. Dashboard Alert
- Red background to draw attention
- Clear message about stalled customers
- Direct link to stalled customers page

### 2. Statistics Cards
- Total stalled customers count
- Never contacted count
- Average days since contact

### 3. Filter Controls
- Search box for customer/company names
- Sales rep dropdown
- Time period selector
- Refresh button

### 4. Sales Rep Summary
- Cards showing stalled customers per rep
- Visual representation of workload

### 5. Customer List
- Customer photo/avatar
- Name and contact information
- Company association
- Assigned sales rep
- Last contact date or "Never contacted"
- Quick action buttons

### 6. Responsive Design
- Mobile-friendly layout
- Touch-friendly buttons
- Collapsible sections on smaller screens

This UI design provides a clean, intuitive interface that makes it "super easy" to identify and manage stalled customers as requested in the original issue.