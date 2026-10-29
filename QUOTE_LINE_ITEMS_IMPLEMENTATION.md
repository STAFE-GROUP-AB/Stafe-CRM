# Quote Builder Line Item Management Implementation

## Overview
A comprehensive line item management system has been implemented for the Stafe CRM Quote Builder. The system allows users to dynamically manage products/services within quotes with full calculation support for discounts, taxes, and totals.

## Features Implemented

### 1. Line Item Management
- **Add Line Items**: Dynamically add new products/services to quotes
- **Edit Line Items**: Inline editing of existing line items
- **Delete Line Items**: Confirmation dialog before deletion
- **Reorder Line Items**: Move items up/down with visual controls
- **Auto-save**: Line items are persisted to database automatically

### 2. Line Item Fields
Each line item includes:
- **Name**: Product/service name (required)
- **Description**: Detailed description (optional)
- **SKU**: Stock Keeping Unit / product code (optional)
- **Quantity**: Number of units (required, min: 1)
- **Unit Price**: Price per unit (required, min: 0)
- **Discount**: Either percentage (0-100%) OR fixed amount
- **Tax Rate**: Per-line tax rate stored in custom_fields (optional)
- **Line Total**: Automatically calculated

### 3. Calculation Features

#### Line-Level Calculations
- Subtotal = Quantity × Unit Price
- Discount (percentage) = Subtotal × (Discount% / 100)
- Discount (fixed) = Fixed discount amount
- Line Total = Subtotal - Discount

#### Quote-Level Calculations
- **Subtotal**: Sum of all line subtotals (Qty × Unit Price)
- **Line Discounts**: Sum of all line-level discounts
- **Global Discount**: Applied to subtotal after line discounts
  - Can be percentage-based or fixed amount
  - User selectable via dropdown
- **Tax**: Applied to total after all discounts
  - Global tax rate (percentage)
  - Applied uniformly across quote
- **Grand Total**: Subtotal - All Discounts + Tax

### 4. User Interface

#### Line Items Table
- Responsive table design with dark mode support
- Columns: Order Controls, Product/Service, SKU, Qty, Unit Price, Discount, Line Total, Actions
- Inline editing mode with live calculations
- Visual feedback for hover states and actions
- Empty state with call-to-action

#### Summary Section
- Clear breakdown of all calculations
- Global discount configuration
- Tax rate input
- Real-time total updates
- "Update Quote Totals" button to persist changes

#### Modal Integration
- Full-screen modal for line item management
- Embedded within Quote Builder workflow
- Accessible via "Line Items" button on each quote
- Automatically opens after quote creation/editing

### 5. Dark Mode Support
All components fully support dark mode with:
- Proper background colors (dark:bg-gray-800)
- Text contrast (dark:text-white, dark:text-gray-400)
- Border colors (dark:border-gray-700)
- Hover states (dark:hover:bg-gray-700)
- Input styling (dark:bg-gray-700)

## Technical Implementation

### Files Created/Modified

#### New Files
1. **app/Livewire/SalesEnablement/QuoteLineItemManager.php**
   - Livewire component for line item management
   - Handles CRUD operations for line items
   - Calculates totals and updates quote

2. **resources/views/livewire/sales-enablement/quote-line-item-manager.blade.php**
   - Blade view for line item management UI
   - Responsive table with inline editing
   - Summary section with calculations
   - Delete confirmation modal

#### Modified Files
1. **app/Livewire/QuoteBuilder.php**
   - Added `$showLineItemsModal` property
   - Added `$quoteForLineItems` property
   - Added `manageLineItems()` method
   - Added `closeLineItemsModal()` method
   - Modified `saveQuote()` to auto-open line items

2. **resources/views/livewire/sales-enablement/quote-builder.blade.php**
   - Added "Line Items" action button
   - Added line items modal integration
   - Embedded QuoteLineItemManager component

### Database Structure

#### Existing Tables Used
- **quotes**: Stores quote header information
  - subtotal, tax_amount, discount_amount, total_amount

- **quote_items**: Stores individual line items
  - name, description, sku
  - quantity, unit_price
  - discount_percent, discount_amount
  - line_total, sort_order
  - custom_fields (JSON - stores tax_rate)

### Key Methods

#### QuoteLineItemManager Component

**CRUD Operations:**
- `addLineItem()`: Adds new blank line item
- `editLineItem($index)`: Enables inline editing
- `saveLineItem($index)`: Persists changes to database
- `removeLineItem($index)`: Initiates delete confirmation
- `confirmDelete()`: Executes deletion
- `cancelDelete()`: Cancels deletion

**Ordering:**
- `moveUp($index)`: Moves item up in list
- `moveDown($index)`: Moves item down in list
- `reorderItems()`: Updates sort_order in database

**Calculations:**
- `calculateLineTotal($item)`: Calculates single line total
- `calculateTotals()`: Updates quote totals
- Computed properties for summary display:
  - `getSubtotalProperty()`
  - `getLineDiscountTotalProperty()`
  - `getGlobalDiscountAmountProperty()`
  - `getTotalDiscountProperty()`
  - `getTaxAmountProperty()`
  - `getGrandTotalProperty()`

## Usage Flow

### Creating a Quote with Line Items
1. Navigate to Quote Builder (`/quotes`)
2. Click "New Quote"
3. Fill in quote details (title, deal, company, etc.)
4. Click "Create Quote"
5. Line Items modal automatically opens
6. Add line items with "Add Line Item" button
7. Fill in product/service details
8. Click green checkmark to save each item
9. Configure global discount and tax rate
10. Click "Update Quote Totals"
11. Click "Done" to close modal

### Editing Existing Quote Line Items
1. Navigate to Quote Builder
2. Find the quote in the list
3. Click "Line Items" button
4. Modify existing items or add new ones
5. Use up/down arrows to reorder
6. Update totals and close

## Design Decisions

### Why Store Tax in Custom Fields?
Tax rate per line item is stored in the `custom_fields` JSON column rather than adding a dedicated column. This provides flexibility for future extensions without additional migrations.

### Why Separate Line and Global Discounts?
- **Line discounts**: Item-specific promotions (e.g., "10% off this product")
- **Global discounts**: Quote-wide promotions (e.g., "Early bird discount")
This separation provides clear tracking and better reporting.

### Why Auto-open Line Items After Quote Creation?
Users typically want to add line items immediately after creating a quote. Auto-opening the modal streamlines the workflow and reduces clicks.

### Why Inline Editing vs Separate Form?
Inline editing keeps the user in context and allows quick modifications without modal stacking or page navigation.

## Styling & Theme

### Color Scheme
- **Primary actions**: Amber (Stafe theme color)
  - `bg-amber-600`, `hover:bg-amber-700`
  - `text-amber-600`, `dark:text-amber-400`
- **Success**: Green
- **Danger**: Red
- **Info**: Indigo

### Responsive Design
- Mobile-first approach
- Horizontal scroll for table on small screens
- Full-width modals on mobile
- Stacked layouts for summary section

### Accessibility
- ARIA labels for screen readers
- Focus states on interactive elements
- Semantic HTML structure
- Keyboard navigation support

## Future Enhancements

### Potential Additions
1. **Product Catalog Integration**
   - Autocomplete from products table
   - Pre-fill SKU, description, price from catalog

2. **Tax Rate Library**
   - Predefined tax rates by jurisdiction
   - Automatic tax calculation based on customer location

3. **Bulk Operations**
   - Import line items from CSV
   - Clone line items within quote
   - Apply discount to multiple items

4. **Advanced Calculations**
   - Tiered pricing (quantity breaks)
   - Bundle pricing
   - Recurring vs one-time items

5. **Line Item Notes**
   - Internal notes per item
   - Customer-facing notes

6. **PDF Export**
   - Generate professional quote PDFs
   - Include line items with calculations

## Testing Checklist

- [ ] Create new quote and add line items
- [ ] Edit existing line items
- [ ] Delete line items with confirmation
- [ ] Reorder line items (up/down)
- [ ] Calculate line totals with percentage discount
- [ ] Calculate line totals with fixed discount
- [ ] Apply global percentage discount
- [ ] Apply global fixed discount
- [ ] Apply tax rate
- [ ] Verify quote totals update correctly
- [ ] Test dark mode rendering
- [ ] Test responsive layout on mobile
- [ ] Verify inline validation errors display
- [ ] Test empty state (no line items)
- [ ] Close and reopen line items modal
- [ ] Test with multiple quotes
- [ ] Verify sort order persists

## Support & Maintenance

### Common Issues

**Line totals not updating:**
- Ensure `wire:model.live` is used for quantity, unit_price, discount fields
- Check browser console for JavaScript errors
- Verify Livewire scripts are loaded

**Modal not opening:**
- Verify `$showLineItemsModal` and `$quoteForLineItems` properties are set
- Check for JavaScript conflicts
- Ensure modal backdrop is clickable

**Totals incorrect:**
- Review calculation methods in QuoteLineItemManager
- Verify discount type (percentage vs fixed)
- Check tax rate is decimal (e.g., 25 for 25%)

### Browser Compatibility
- Chrome 90+
- Firefox 88+
- Safari 14+
- Edge 90+

## Contributors
Implemented by Claude Code (Wilma - FLUX UI Expert) for Stafe CRM

## License
Part of Stafe CRM - Internal Implementation
