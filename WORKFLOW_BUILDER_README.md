# Visual Workflow Builder - Implementation Guide

## Overview

A complete drag-and-drop visual workflow builder has been implemented for the Stafe CRM. This feature allows users to create complex automation workflows using an intuitive visual interface.

## Features Implemented

### 1. Drag-and-Drop Interface
- **Node Palette**: Left sidebar with draggable workflow nodes
- **Canvas Area**: Main workspace where nodes can be dropped and arranged
- **Visual Connections**: Drag connections between nodes to define workflow flow
- **Pan & Zoom**: Navigate large workflows easily

### 2. Node Types

#### Trigger Nodes (Purple)
- **Event Trigger**: Starts workflow based on system events
- Position: Start of workflow
- Icon: Bell

#### Action Nodes (Blue)
- **Send Email**: Send automated emails
- **Create Task**: Generate tasks automatically
- **Update Field**: Modify record fields
- **Send Notification**: Send in-app notifications
- Icon: Envelope, Calendar, Edit, Bell

#### Condition Nodes (Green)
- **If/Else Branch**: Conditional workflow branching
- Configure: field, operator, value
- Icon: Check badge

#### Delay Nodes (Orange)
- **Wait/Delay**: Pause workflow execution
- Configure: duration (minutes, hours, days)
- Icon: Clock

### 3. Node Configuration
- **Right Sidebar**: Two-tab interface
  - **Workflow Tab**: Overall workflow settings
  - **Node Config Tab**: Selected node configuration
- **Real-time Updates**: Changes sync immediately with Livewire

### 4. Visual Features
- **Color-coded Nodes**: Each type has distinct gradient colors
- **Hover Effects**: Smooth scale and shadow transitions (200ms)
- **Connection Lines**: Bezier curves with arrow indicators
- **Grid Background**: Subtle dot pattern for alignment
- **Dark Mode Support**: Full dark mode with appropriate colors

### 5. Workflow Persistence
- **JSON Storage**: Visual configuration stored in `visual_config` column
- **Auto-sync**: Nodes converted to WorkflowSteps on save
- **Bidirectional**: Can load existing step-based workflows

## Files Created/Modified

### New Files
1. `/database/migrations/2025_12_30_100000_add_visual_config_to_workflow_templates_table.php`
   - Adds `visual_config` JSON column to store visual layout

2. `/app/Livewire/WorkflowBuilder.php`
   - Main Livewire component
   - Handles node/connection management
   - Syncs visual config with workflow steps

3. `/resources/views/livewire/workflow-builder.blade.php`
   - Complete visual builder interface
   - Alpine.js drag-drop implementation
   - Responsive design with dark mode

### Modified Files
1. `/app/Models/WorkflowTemplate.php`
   - Added `visual_config` to fillable and casts

2. `/routes/web.php`
   - Added routes for visual builder
   - `/workflows/builder` - Create new workflow
   - `/workflows/{workflow}/builder` - Edit existing workflow

3. `/resources/views/workflows/index.blade.php`
   - Added "Visual Builder" button
   - Added "Builder" link for each workflow

4. `/resources/views/workflows/create.blade.php`
   - Added promotional section for visual builder

5. `/resources/views/workflows/edit.blade.php`
   - Added "Open Visual Builder" button

6. `/resources/views/workflows/show.blade.php`
   - Added "Visual Builder" button to header

## How to Use

### Creating a New Workflow

1. Navigate to Workflows → Click "Visual Builder"
2. Drag nodes from the left palette onto the canvas
3. Connect nodes by dragging from output port to input port
4. Click nodes to configure them in the right sidebar
5. Set workflow name and settings in the Workflow tab
6. Click "Save Workflow"

### Editing Existing Workflow

1. Navigate to Workflows → Click "Builder" on any workflow
2. Visual representation loads automatically
3. Modify nodes, connections, and configuration
4. Click "Save Workflow"

### Node Operations

**Add Node**: Drag from palette to canvas
**Move Node**: Click and drag node
**Delete Node**: Hover over node, click red X button
**Configure Node**: Click node, edit in right sidebar
**Connect Nodes**: Drag from output port (right) to input port (left)
**Delete Connection**: Click on connection line

### Canvas Controls

**Zoom In/Out**: Use +/- buttons in toolbar
**Reset View**: Click "Reset View" button
**Pan**: Click and drag on empty canvas area

## Technical Implementation

### Alpine.js Components

#### workflowBuilder(nodesWire, connectionsWire)
Main component managing the visual builder:
- `nodes`: Array of workflow nodes with position and config
- `connections`: Array of connections between nodes
- `zoom`: Current zoom level (0.5 - 2.0)
- `panX/panY`: Pan offset for canvas navigation
- `selectedNode`: Currently selected node ID

Key methods:
- `handleDrop()`: Create node from palette drag
- `startDragNode()`: Begin node movement
- `startConnection()`: Initiate connection creation
- `getConnectionPath()`: Calculate bezier curve for connections

#### nodeConfigEditor()
Manages node configuration panel:
- Watches for selected node changes
- Updates configuration fields
- Saves config back to Livewire

### Livewire Component

**WorkflowBuilder.php** features:
- `mount()`: Load existing workflow or initialize new
- `loadWorkflowData()`: Convert visual config to nodes
- `convertStepsToNodes()`: Import from existing steps
- `save()`: Persist workflow with visual config
- `syncWorkflowSteps()`: Convert nodes to workflow steps

### Animation Design Principles

All animations follow interaction design best practices:

1. **Duration**: 200ms for most interactions (fast but noticeable)
2. **Easing**: `ease-out` for entrances, `ease-in` for exits
3. **Purposeful Motion**: Every animation provides feedback
4. **Reduced Motion**: Uses Tailwind's transition utilities that respect `prefers-reduced-motion`

### Micro-interactions

- **Button Hover**: Scale to 105%, shadow lift (200ms)
- **Node Hover**: Shadow increase, delete button fade-in (200ms)
- **Node Drag**: Smooth position updates (100ms ease-out)
- **Connection Draw**: Dashed line follows mouse
- **Node Selection**: Ring highlight appears instantly
- **Tab Switch**: Instant content swap (no animation for clarity)

### Visual Feedback

- **Dragging Node**: Cursor changes to `grabbing`
- **Connecting**: Dashed line from source to cursor
- **Hover Node**: Shadow and scale increase
- **Selected Node**: Amber ring (2px)
- **Delete Confirmation**: Browser confirm dialog

## Database Schema

### workflow_templates table
```php
'visual_config' => 'json' // Stores:
[
    'nodes' => [
        [
            'id' => 'node-123',
            'type' => 'action',
            'subtype' => 'send_email',
            'label' => 'Send Email',
            'x' => 100,
            'y' => 200,
            'config' => [...]
        ]
    ],
    'connections' => [
        [
            'id' => 'conn-123-456',
            'from' => 'node-123',
            'to' => 'node-456'
        ]
    ]
]
```

## Styling & Theming

### Color Scheme (Stafe Amber Theme)
- Primary Actions: `amber-600` / `amber-500` (dark)
- Trigger Nodes: `purple-500` to `purple-600` gradient
- Action Nodes: `blue-500` to `blue-600` gradient
- Condition Nodes: `green-500` to `green-600` gradient
- Delay Nodes: `orange-500` to `orange-600` gradient

### Dark Mode
All colors have dark mode variants:
- Backgrounds: `gray-800`, `gray-900`
- Borders: `gray-700`
- Text: `white`, `gray-300`
- Node gradients: Darker shades in dark mode

## Performance Considerations

1. **GPU Acceleration**: Uses `transform` for animations
2. **Debouncing**: Node position updates use requestAnimationFrame
3. **Virtual Canvas**: Only renders visible area with pan/zoom
4. **Efficient Updates**: Alpine.js reactive updates only changed nodes

## Browser Compatibility

- Chrome/Edge: Full support
- Firefox: Full support
- Safari: Full support
- Mobile: Touch events supported for drag operations

## Future Enhancements

Potential improvements:
1. Multi-select nodes
2. Copy/paste nodes
3. Undo/redo functionality
4. Node templates/presets
5. Export/import workflow JSON
6. Collaborative editing
7. Workflow validation
8. Auto-layout algorithm
9. Minimap for large workflows
10. Keyboard shortcuts

## Troubleshooting

### Nodes not saving
- Check browser console for Livewire errors
- Ensure `visual_config` column exists (run migration)
- Verify WorkflowTemplate model has `visual_config` in fillable

### Connections not drawing
- Check that nodes have valid IDs
- Ensure connection endpoints exist
- Verify SVG is rendering (check browser inspector)

### Dark mode not working
- Ensure Tailwind dark mode is configured
- Check that `dark:` classes are present
- Verify theme toggle functionality

## Support

For issues or questions:
1. Check browser console for errors
2. Verify database migration ran successfully
3. Clear Laravel cache: `php artisan cache:clear`
4. Check Livewire version compatibility

## Credits

Built with:
- Laravel 12
- Livewire 3
- Alpine.js
- Tailwind CSS 4
- Following Anthropic's Claude interaction design principles
