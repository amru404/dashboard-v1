# License Creation UI Flow - Visual Guide

## Step 1: License Index Page

```
┌─────────────────────────────────────────────────────────────┐
│ Licenses                                                    │
│ Installer-facing license records...                         │
│                                    [Generate Bulk] [New ...] │
└─────────────────────────────────────────────────────────────┘

┌─ Product A (Parent) ────────────────────────────────────────┐
│ [►] 2 licenses         [Add License]                        │
│                                                              │
│ └─ Sub A1              1 license   [Add License]            │
│    • KEY1 (****-1234)  User: John  Status: Active          │
│                                                              │
│ └─ Sub A2              0 licenses                           │
└─────────────────────────────────────────────────────────────┘

Click "Add License" or "Generate Bulk" to create new license.
```

## Step 2: Create License Form

```
┌─────────────────────────────────────────────────────────────┐
│ New License                                                 │
│ Create an installer-facing license...                       │
└─────────────────────────────────────────────────────────────┘

┌─ Create License Form ───────────────────────────────────────┐
│                                                              │
│ Customer User *                                              │
│ ┌──────────────────────────────────────────────────────┐   │
│ │ Select customer...                               ▼  │   │
│ │ John Doe - john@example.com - ACME Corp        ✓   │   │
│ └──────────────────────────────────────────────────────┘   │
│                                                              │
│ License Type *                                               │
│ ┌──────────────────────────────────────────────────────┐   │
│ │ Pro License (PRO)                              ▼    │   │
│ └──────────────────────────────────────────────────────┘   │
│                                                              │
│ Parent Product *                                             │
│ ┌──────────────────────────────────────────────────────┐   │
│ │ Product A                                      ▼    │   │
│ └──────────────────────────────────────────────────────┘   │
│ Select parent product only. Sub-products can be...          │
│                                                              │
│ Device Limit                                                │
│ ┌──────────────┐                                            │
│ │      5       │  (Leave blank for unlimited)               │
│ └──────────────┘                                            │
│                                                              │
│ Expiry Date                                                 │
│ ┌──────────────┐                                            │
│ │ 2026-12-31   │  (Leave blank if never expires)            │
│ └──────────────┘                                            │
│                                                              │
│ [Create License]  [Cancel]                                  │
└─────────────────────────────────────────────────────────────┘
```

**Result:** License created → Redirect to show page

## Step 3: License Show Page (Parent)

```
┌─────────────────────────────────────────────────────────────┐
│ License Details                                             │
│ John Doe - Product A                                        │
│                          [Add sub-product keys] [Back...]   │
└─────────────────────────────────────────────────────────────┘

┌─ License Information ───────────────────────────────────────┐
│                                                              │
│ Customer          │  License Type                          │
│ John Doe          │  Pro License                            │
│ john@example.com  │  (PRO)                                  │
│                   │                                          │
│ Organization      │  Product                               │
│ ACME Corp         │  Product A                             │
│                   │  Parent Only                            │
│                   │                                          │
│ Expiry            │  Activations                            │
│ Never             │  0 active / 5                           │
│ No expiry         │                                          │
│                   │                                          │
│ License Key                                                 │
│ ****-****-****-1234  [Show] [Reveal]                       │
│                                                              │
└─────────────────────────────────────────────────────────────┘

┌─ Sub-product Licenses ──────────────────────────────────────┐
│                                                              │
│ Sub-Product | License Key      | Activations | Status      │
│─────────────┼─────────────────┼─────────────┼─────────────│
│ No sub-product keys added yet.                             │
│                                                              │
└─────────────────────────────────────────────────────────────┘

Click "Add sub-product keys" to add licenses for sub-products.
```

## Step 4: Add Sub-Product Keys Form

```
┌─────────────────────────────────────────────────────────────┐
│ Add Sub-Product Keys                                        │
│ Product A - Assigned to John Doe                            │
└─────────────────────────────────────────────────────────────┘

┌─ LEFT PANEL ────────────────────┬─ RIGHT PANEL ───────────┐
│                                 │                         │
│ Select Sub-Products *           │ Information             │
│                                 │                         │
│ ┌───────────────────────────┐  │ Parent Product          │
│ │ [✓] Product A → Sub 1     │  │ Product A               │
│ │     Qty: [___2___]        │  │                         │
│ │                           │  │ Customer                │
│ │ [✓] Product A → Sub 2     │  │ John Doe                │
│ │     Qty: [___1___]        │  │                         │
│ │                           │  │ License Type            │
│ │ [✓] Product A → Sub 3     │  │ Pro License             │
│ │     Qty: [___3___]        │  │                         │
│ │                           │  │ Device Limit            │
│ │ [ ] Product A → Sub 4     │  │ 5 devices               │
│ │                           │  │                         │
│ └───────────────────────────┘  │ Expires                 │
│                                 │ 2026-12-31              │
│ Total licenses: 6               │                         │
│                                 │ ───────────────────────│
│ License Keys                    │                         │
│                                 │ Select sub-products    │
│ ┌─────────────────────────────┐ │ and set quantity for   │
│ │ Sub-Product 1, Key 1        │ │ each...               │
│ │ [_______KEY1_______]  [Gen] │ │                         │
│ │                             │ │                         │
│ │ Sub-Product 1, Key 2        │ │                         │
│ │ [_______KEY2_______]  [Gen] │ │                         │
│ │                             │ │                         │
│ │ Sub-Product 2, Key 1        │ │                         │
│ │ [_______KEY3_______]  [Gen] │ │                         │
│ │                             │ │                         │
│ │ Sub-Product 3, Key 1        │ │                         │
│ │ [_______KEY4_______]  [Gen] │ │                         │
│ │                             │ │                         │
│ │ Sub-Product 3, Key 2        │ │                         │
│ │ [_______KEY5_______]  [Gen] │ │                         │
│ │                             │ │                         │
│ │ Sub-Product 3, Key 3        │ │                         │
│ │ [_______KEY6_______]  [Gen] │ │                         │
│ │                             │ │                         │
│ └─────────────────────────────┘ │                         │
│                                 │                         │
│ ╔═══════════════════════════╗  │                         │
│ ║ Total keys: 6             ║  │                         │
│ ╚═══════════════════════════╝  │                         │
│                                 │                         │
│ [Save Keys]  [Cancel]           │                         │
└─────────────────────────────────┴─────────────────────────┘

**Interactions:**
- Unchecking a sub-product hides its quantity input
- Checking a sub-product shows its quantity input (default: 1)
- Changing qty updates key form count in real-time
- Total keys counter updates automatically
- [Gen] button generates cryptographic key
- Can manually type keys instead
```

## Step 5: After Keys Added

```
┌─ License Show Page (Updated) ───────────────────────────────┐
│                                                              │
│ ... (same as before) ...                                    │
│                                                              │
│ ┌─ Sub-product Licenses ──────────────────────────────────┐ │
│ │                                                          │ │
│ │ Sub-Product │ License Key    │ Activations │ Status     │ │
│ │─────────────┼────────────────┼─────────────┼──────────│ │
│ │ Sub 1       │ ****-****-1234 │ 0/5         │ Active   │ │
│ │ Sub 1       │ ****-****-5678 │ 0/5         │ Active   │ │
│ │ Sub 2       │ ****-****-9012 │ 0/5         │ Active   │ │
│ │ Sub 3       │ ****-****-3456 │ 0/5         │ Active   │ │
│ │ Sub 3       │ ****-****-7890 │ 0/5         │ Active   │ │
│ │ Sub 3       │ ****-****-2468 │ 0/5         │ Active   │ │
│ │                                                    Delete  │ │
│ │                                                          │ │
│ └──────────────────────────────────────────────────────────┘ │
│                                                              │
└─────────────────────────────────────────────────────────────┘
```

## Data Flow Summary

```
┌──────────────────────────────────────────────────────────────┐
│ Admin creates Parent License                                 │
└───────────────────────┬──────────────────────────────────────┘
                        │
                        ▼
         ┌──────────────────────────────────────┐
         │ Parent License (No Key)              │
         │ - Product A                          │
         │ - User: John Doe                     │
         │ - Type: Pro                          │
         │ - Device Limit: 5                    │
         │ - Expires: 2026-12-31               │
         └──────────────┬───────────────────────┘
                        │
                        │ Admin clicks "Add sub-product keys"
                        │
                        ▼
         ┌──────────────────────────────────────┐
         │ Select Sub-Products + Qty            │
         │ - Sub 1: Qty 2                       │
         │ - Sub 2: Qty 1                       │
         │ - Sub 3: Qty 3                       │
         └──────────────┬───────────────────────┘
                        │
                        │ Generates 6 key input forms
                        │
                        ▼
         ┌──────────────────────────────────────┐
         │ Enter/Generate 6 Keys                │
         │ - KEY1, KEY2, KEY3, KEY4, KEY5, KEY6 │
         └──────────────┬───────────────────────┘
                        │
                        │ Submit form
                        │
                        ▼
         ┌──────────────────────────────────────┐
         │ Creates 6 Sub-Product Licenses       │
         │ All inherit parent settings          │
         │ Each gets unique key                 │
         └──────────────┬───────────────────────┘
                        │
                        ▼
         ┌──────────────────────────────────────┐
         │ Show Page Updated                    │
         │ - Parent license listed              │
         │ - 6 sub-product licenses in table    │
         └──────────────────────────────────────┘
```

## Key Features Highlighted

### Feature 1: Hierarchical Display
```
Product A (Parent)
├── Product A → Sub 1
├── Product A → Sub 2  
└── Product A → Sub 3
    ├── Product A → Sub 3 → SubSub 1
    └── Product A → Sub 3 → SubSub 2
```

### Feature 2: Dynamic Form Generation
```
Selected:
- Sub 1 (Qty: 2)
- Sub 2 (Qty: 1)
- Sub 3 (Qty: 3)

Generated Forms:
1. Sub 1, Key 1 [input] [Gen]
2. Sub 1, Key 2 [input] [Gen]
3. Sub 2, Key 1 [input] [Gen]
4. Sub 3, Key 1 [input] [Gen]
5. Sub 3, Key 2 [input] [Gen]
6. Sub 3, Key 3 [input] [Gen]

Total: 6
```

### Feature 3: Table Display
```
Easy to scan, no card clutter:

Sub-Product │ License Key │ Activations │ Status  │ Action
────────────┼─────────────┼─────────────┼─────────┼────────
Sub 1       │ ****-1234   │ 0/5         │ Active  │ Delete
Sub 1       │ ****-5678   │ 0/5         │ Active  │ Delete
Sub 2       │ ****-9012   │ 0/5         │ Active  │ Delete
```

## Keyboard Shortcuts (Optional Enhancement)

Could add:
- `G` - Generate key (when focused on input)
- `Enter` - Generate key for current field
- `Tab` - Navigate between forms

## Mobile Responsive

Forms adapt for mobile:
- Full-width inputs on small screens
- Stacked buttons
- Collapsible sidebar info
- Scrollable table with horizontal scroll

## Accessibility Features

Implemented:
- Form labels with `for` attributes
- Error messages linked to inputs
- Keyboard navigation
- ARIA labels for icon buttons
- High contrast colors
- Proper heading hierarchy
