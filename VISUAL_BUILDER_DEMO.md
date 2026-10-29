# Visual Workflow Builder - Visual Demo

## Interface Layout

```
┌─────────────────────────────────────────────────────────────────────────────┐
│ ← Back to Workflows    Create Workflow                     [Save Workflow]  │
│                        Drag nodes from palette to build...                   │
├─────────────┬────────────────────────────────────────────┬──────────────────┤
│             │                                            │                  │
│ NODE        │           CANVAS AREA                      │  CONFIGURATION   │
│ PALETTE     │                                            │                  │
│             │  ┌──────────────────────┐                  │  ┌─────────────┐ │
│ ┌─────────┐ │  │  [🔔] Event Trigger │                  │  │ Workflow    │ │
│ │Trigger  │ │  └──────────┬───────────┘                  │  │ Node Config │ │
│ └─────────┘ │             │                              │  └─────────────┘ │
│ [🔔] Event  │             ↓                              │                  │
│             │  ┌──────────────────────┐                  │  Name:           │
│ ┌─────────┐ │  │  [✉️] Send Email     │                  │  [____________] │
│ │Actions  │ │  └──────────┬───────────┘                  │                  │
│ └─────────┘ │             │                              │  Description:    │
│ [✉️] Email   │             ↓                              │  [____________] │
│ [📅] Task    │  ┌──────────────────────┐                  │                  │
│ [✏️] Update  │  │  [⏰] Wait 1 Day     │                  │  Trigger:        │
│ [🔔] Notify  │  └──────────────────────┘                  │  [Event ▼]      │
│             │                                            │                  │
│ ┌─────────┐ │  Grid background (subtle dots)             │  □ Active       │
│ │Logic    │ │                                            │                  │
│ └─────────┘ │  [+] [-] Reset View                        │  ───────────────│
│ [✓] If/Else │                                            │  Nodes: 3       │
│             │                                            │  Connections: 2  │
│ ┌─────────┐ │                                            │                  │
│ │Timing   │ │                                            │                  │
│ └─────────┘ │                                            │                  │
│ [⏰] Delay   │                                            │                  │
│             │                                            │                  │
└─────────────┴────────────────────────────────────────────┴──────────────────┘
```

## Node Visual Design

### Trigger Node (Purple)
```
┌─────────────────────────────┐
│ [🔔] TRIGGER           [×]  │  ← Delete button (hover)
│                             │
│ Event Trigger               │  ← Node label
│                             │
│ ⚪                        ⚪ │  ← Connection ports
│ In                      Out │
└─────────────────────────────┘
  ↑ Gradient: purple-500 → purple-600
  ↑ Shadow increases on hover
  ↑ Scale 105% on hover (200ms)
```

### Action Node (Blue)
```
┌─────────────────────────────┐
│ [✉️] ACTION            [×]  │
│                             │
│ Send Email                  │
│                             │
│ ⚪                        ⚪ │
│ In                      Out │
└─────────────────────────────┘
  ↑ Gradient: blue-500 → blue-600
```

### Condition Node (Green)
```
┌─────────────────────────────┐
│ [✓] CONDITION          [×]  │
│                             │
│ If/Else Branch              │
│                             │
│ ⚪                        ⚪ │
│ In                      Out │
└─────────────────────────────┘
  ↑ Gradient: green-500 → green-600
```

### Delay Node (Orange)
```
┌─────────────────────────────┐
│ [⏰] DELAY              [×]  │
│                             │
│ Wait 1 Day                  │
│                             │
│ ⚪                        ⚪ │
│ In                      Out │
└─────────────────────────────┘
  ↑ Gradient: orange-500 → orange-600
```

## Connection Lines

```
Node A ────────────────────────────▶ Node B
       ↑                           ↑
   Bezier curve              Arrow indicator
   (smooth S-curve)          (shows direction)

   - Gray in light mode
   - Lighter gray in dark mode
   - Amber on hover
   - Click to delete
```

## Interactive States

### Node States
```
Default:     [Node]           - Base gradient, subtle shadow
Hover:       [Node]↗          - Scale 105%, larger shadow (200ms)
Dragging:    [Node]...        - Following cursor, semi-transparent
Selected:    [⚪Node⚪]         - Amber ring (2px), ring-amber-500
```

### Connection States
```
Normal:      A ────▶ B        - Gray line (2px)
Hover:       A ═══▶ B         - Amber line (2px)
Drawing:     A - - -          - Dashed line following cursor
```

### Canvas States
```
Default:     ·  ·  ·  ·       - Subtle dot grid
Panning:     👆               - Grab cursor
Zoomed In:   [Larger view]    - Scale 1.1 - 2.0
Zoomed Out:  [Smaller view]   - Scale 0.5 - 0.9
```

## Color Palette

### Light Mode
- Background: `gray-50`
- Canvas: `white`
- Nodes: Gradient from base to darker
- Text: `gray-900`
- Borders: `gray-200`

### Dark Mode
- Background: `gray-900`
- Canvas: `gray-800`
- Nodes: Darker gradients
- Text: `white`
- Borders: `gray-700`

## Interaction Flow Examples

### Creating a Simple Workflow

```
Step 1: Drag "Event Trigger" to canvas
┌────────────┐
│ [🔔] Event │
└────────────┘

Step 2: Drag "Send Email" below it
┌────────────┐
│ [🔔] Event │
└────────────┘
┌────────────┐
│ [✉️] Email  │
└────────────┘

Step 3: Connect them (drag from out to in)
┌────────────┐
│ [🔔] Event │
└─────┬──────┘
      │
      ↓
┌────────────┐
│ [✉️] Email  │
└────────────┘

Step 4: Configure email node
(Click node → Edit in right sidebar)

Step 5: Save workflow
(Click "Save Workflow" button)
```

### Complex Workflow Example

```
┌────────────────┐
│  Event Trigger │ When new contact created
└────────┬───────┘
         │
         ↓
┌────────────────┐
│  If/Else       │ Is high value?
└───┬────────┬───┘
    │        │
Yes │        │ No
    │        │
    ↓        ↓
┌───────┐  ┌──────────┐
│ Email │  │ Wait 1hr │
│ Sales │  └────┬─────┘
└───────┘       │
                ↓
           ┌──────────┐
           │ Email    │
           │ Nurture  │
           └──────────┘
```

## Animations Timeline

```
Hover Animation (200ms):
0ms  ─────────────────▶  200ms
[Node]               [Node ↗]
                     ↑
               Scale: 1.0 → 1.05
               Shadow: sm → lg
               Easing: ease-out

Drag Animation (100ms):
0ms  ─────────────────▶  100ms
[Node @ (x1,y1)]    [Node @ (x2,y2)]
                     ↑
               Position update
               Easing: ease-out

Connection Draw (Real-time):
Each mouse move updates path instantly
No delay, follows cursor exactly
```

## Responsive Behavior

### Desktop (lg+)
- 3-column layout (2-7-3 grid)
- Full palette visible
- Large canvas area
- Sticky sidebars

### Tablet (md)
- 2-column layout (collapsed palette)
- Medium canvas
- Floating configuration

### Mobile (sm)
- Single column (stacked)
- Tabs for palette/canvas/config
- Touch-optimized drag operations

## Accessibility Features

```
Keyboard Navigation:
Tab       → Navigate between controls
Enter     → Activate button
Esc       → Cancel connection draw
Delete    → Delete selected node

Screen Reader:
- ARIA labels on all interactive elements
- Role="button" on clickable nodes
- Descriptive alt text for icons

Reduced Motion:
- Respects prefers-reduced-motion
- Uses Tailwind transition utilities
- Can disable animations system-wide
```

## Performance Indicators

```
Loading:    ⚡ < 100ms initialization
Dragging:   🚀 60fps smooth movement
Connecting: ⚡ Real-time line drawing
Saving:     💾 < 500ms workflow save
```

## Success Indicators

```
✓ Flash message appears (green bar at top)
✓ Redirect to workflow detail page
✓ Visual config saved to database
✓ Workflow steps created/updated
```

## Visual Feedback Examples

### Save Success
```
┌─────────────────────────────────────────────┐
│ ✓ Workflow saved successfully.              │
└─────────────────────────────────────────────┘
     ↑
  Slides down from top
  Green background
  Auto-dismiss after 3s
```

### Delete Confirmation
```
┌─────────────────────────────────────┐
│  Delete this node?                  │
│                                     │
│  [Cancel]           [Delete]        │
└─────────────────────────────────────┘
     ↑
  Native browser confirm
  Accessible
  Clear action
```

### Validation Error
```
┌─────────────────────────────────────────────┐
│ ⚠ Workflow name is required.                │
└─────────────────────────────────────────────┘
     ↑
  Red background
  Below offending field
  Clear, actionable message
```

This visual guide demonstrates the complete user interface and interaction patterns implemented in the workflow builder.
