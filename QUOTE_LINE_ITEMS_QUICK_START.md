# Quote Line Items - Quick Start Guide

## Accessing the Quote Builder

Navigate to: **`/quotes`** in your browser

Or from the Stafe CRM menu: **Sales Enablement → Quote Builder**

## Creating a Quote with Line Items

### Step 1: Create New Quote
1. Click the **"New Quote"** button
2. Fill in the required fields:
   - Title (required)
   - Deal (required - dropdown)
   - Company (required - auto-filled from deal)
   - Contact (optional - filtered by company)
   - Valid Until (optional - expiration date)
   - Description (optional)
3. Click **"Create Quote"**

### Step 2: Add Line Items (Auto-opens)
The Line Items modal will automatically open after creating the quote.

#### Adding Your First Line Item
1. Click **"Add Line Item"** button
2. Fill in the item details:
   - **Product/Service Name** (required) - e.g., "Web Design Services"
   - **Description** (optional) - e.g., "Custom website design with 5 pages"
   - **SKU** (optional) - e.g., "WEB-001"
   - **Quantity** (required) - e.g., 1
   - **Unit Price** (required) - e.g., 5000.00
   - **Discount** (optional):
     - Either percentage (e.g., 10 for 10% off)
     - OR fixed amount (e.g., 500 for $500 off)
3. Click the **green checkmark** to save

#### Adding More Line Items
Repeat the process to add additional products/services:
- Click "Add Line Item"
- Fill in details
- Save with checkmark

#### Reordering Items
Use the **up/down arrows** on the left side of each row to change the order.

### Step 3: Configure Global Settings

#### Global Discount (Optional)
1. Select **Global Discount Type**:
   - **No Discount** - No additional discount
   - **Percentage** - Discount % applied to subtotal
   - **Fixed Amount** - Fixed dollar amount deducted
2. Enter **Discount Value** if applicable

#### Tax Rate (Optional)
Enter the **Tax Rate** percentage (e.g., 25 for 25% tax)

### Step 4: Update Totals
Click **"Update Quote Totals"** to save all calculations to the quote.

### Step 5: Complete
Click **"Done"** to close the Line Items modal and return to Quote Builder.

## Managing Existing Quote Line Items

### Opening Line Items Manager
1. Find your quote in the Quote Builder list
2. Click the **"Line Items"** button (amber/orange color)
3. The Line Items modal will open

### Editing a Line Item
1. Click the **pencil icon** (edit) next to the line item
2. Modify any fields
3. Click the **green checkmark** to save changes

### Deleting a Line Item
1. Click the **trash icon** (red) next to the line item
2. A confirmation dialog will appear
3. Click **"Delete"** to confirm, or **"Cancel"** to abort

### Viewing Calculations

The **Summary Section** shows:
- **Subtotal**: Sum of all (Quantity × Unit Price)
- **Line Item Discounts**: Sum of individual line discounts
- **Global Discount**: Additional quote-wide discount
- **Total Discounts**: Sum of all discounts
- **Tax**: Tax amount based on tax rate
- **Grand Total**: Final amount (Subtotal - Discounts + Tax)

## Calculation Examples

### Example 1: Simple Quote
- **Product**: Website Hosting
- **Quantity**: 12 (months)
- **Unit Price**: $50/month
- **Line Total**: $600

**Quote Total**: $600

### Example 2: With Line Discount
- **Product**: Website Design
- **Quantity**: 1
- **Unit Price**: $5,000
- **Discount**: 10% (early bird)
- **Line Total**: $4,500 ($5,000 - $500)

**Quote Total**: $4,500

### Example 3: Multiple Items with Global Discount and Tax
**Line Items:**
1. Website Design: 1 × $5,000 = $5,000
2. SEO Services: 1 × $1,500 = $1,500
3. Hosting (12 months): 12 × $50 = $600

**Calculations:**
- Subtotal: $7,100
- Global Discount (5%): -$355
- Subtotal after discount: $6,745
- Tax (25%): +$1,686.25
- **Grand Total**: $8,431.25

## Tips & Best Practices

### Organizing Line Items
- Use the **up/down arrows** to group related items together
- Put high-value items at the top for visibility
- Group recurring vs one-time items

### Discounts
- Use **line item discounts** for product-specific promotions
- Use **global discount** for quote-wide deals (e.g., "10% off entire order")
- Both can be applied simultaneously

### SKU Codes
- Use consistent SKU naming for reporting
- Examples: "WEB-DESIGN-01", "SEO-BASIC", "HOST-ANNUAL"

### Descriptions
- Be specific about what's included
- Mention deliverables, timelines, or limits
- Examples:
  - "Custom website design (up to 5 pages)"
  - "Monthly SEO monitoring and reporting"
  - "Annual hosting with 99.9% uptime guarantee"

## Troubleshooting

### Totals Not Updating
- Click **"Update Quote Totals"** button after making changes
- Ensure you've saved individual line items (green checkmark)

### Can't Edit Line Item
- Make sure you're not already editing another item
- Only one item can be in edit mode at a time

### Discount Not Applied
- Check if you entered percentage OR fixed amount (not both)
- Percentage should be 0-100 (e.g., 10 for 10%)
- Fixed amount is in dollars (e.g., 500 for $500)

### Modal Won't Close
- Click **"Done"** button at the bottom
- Or click the **X** button in the top-right
- Or click outside the modal (on the dark background)

## Keyboard Shortcuts

- **Tab**: Move to next field
- **Shift+Tab**: Move to previous field
- **Enter**: Save line item when editing
- **Escape**: Cancel editing / Close modal

## Dark Mode

The Line Items Manager fully supports dark mode:
- Automatically follows your system/app theme
- Toggle in app settings if available
- All elements remain clearly visible in both light and dark modes

## Next Steps

After adding line items:
1. **Review the quote** totals
2. **Send the quote** to the customer (if status is Draft)
3. **Track quote status** (Draft → Sent → Viewed → Accepted/Rejected)
4. **Duplicate quote** for similar projects (preserves line items)

## Need Help?

- See full documentation: `QUOTE_LINE_ITEMS_IMPLEMENTATION.md`
- Contact support or your system administrator
- Check Laravel logs for errors: `storage/logs/laravel.log`

---

**Happy Quoting!** 🎉
