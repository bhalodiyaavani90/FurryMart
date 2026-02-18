# 🎨 VISUAL DESIGN REFERENCE GUIDE

## COLOR PALETTE

### Order Status Colors:
```
Pending:         #fef3c7 (Light Yellow) / #92400e (Dark Yellow)
Confirmed:       #dbeafe (Light Blue) / #1e40af (Dark Blue)
Processing:      #e0e7ff (Light Indigo) / #4338ca (Dark Indigo)
Shipped:         #fce7f3 (Light Pink) / #9f1239 (Dark Pink)
Out for Delivery:#fef9c3 (Light Amber) / #854d0e (Dark Amber)
Delivered:       #d1fae5 (Light Green) / #065f46 (Dark Green)
Cancelled:       #fee2e2 (Light Red) / #991b1b (Dark Red)
```

### Payment Status Colors:
```
Paid:    Green Gradient (#d1fae5 → #a7f3d0) + Border #34d399
Pending: Yellow Gradient (#fef3c7 → #fde68a) + Border #fbbf24
Failed:  Red Gradient (#fee2e2 → #fecaca) + Border #f87171
```

### Button Gradients:
```
Track Order:       Purple (#667eea → #764ba2)
Continue Shopping: Green (#22c55e → #16a34a)
View Details:      Pink (#f093fb → #f5576c)
Download PDF:      Green (#22c55e → #16a34a)
```

---

## ICON MAPPING

### Order Status Icons:
```
Pending:          fa-clock
Confirmed:        fa-check-circle
Processing:       fa-box-open / fa-cog
Shipped:          fa-truck
Out for Delivery: fa-shipping-fast
Delivered:        fa-check-double
Cancelled:        fa-times-circle / fa-exclamation-triangle
```

### Payment Status Icons:
```
Paid:    fa-check-circle (Green)
Pending: fa-clock (Yellow/Orange)
Failed:  fa-times-circle (Red)
```

### Action Icons:
```
Track:     fa-map-marker-alt
Details:   fa-receipt
PDF:       fa-file-pdf
Shop:      fa-shopping-cart
Payment:   fa-credit-card / fa-wallet
Calendar:  fa-calendar-alt
Box:       fa-box
Location:  fa-map-marker-alt
Phone:     fa-phone
```

---

## LAYOUT STRUCTURE

### Order Card Layout:
```
┌─────────────────────────────────────────────────┐
│ Order #12345          [Status][Payment Status]  │ ← Header
│ Jan 15, 2024                                     │
├─────────────────────────────────────────────────┤
│ 📦 Items: 3                                     │
│ 💰 Total: ₹1,499.00                             │ ← Info Section
│ 💳 Payment: UPI                                 │
├─────────────────────────────────────────────────┤
│ [⚠️ Cancellation Reason: Out of stock]         │ ← Only if cancelled
├─────────────────────────────────────────────────┤
│ [Track Order]  [View Details]                   │ ← Actions
└─────────────────────────────────────────────────┘
```

### Cancelled Card Special:
```
┌═════════════════════════════════════════════════┐ ← RED BORDER (3px)
║ ┌─────────────────────────────────────────────┐ ║
║ │                      [✖ CANCELLED Badge]    │ ║ ← Top right
║ │ Order #12345        [Cancelled][Pending]    │ ║
║ │ Jan 15, 2024                                │ ║
║ ├─────────────────────────────────────────────┤ ║
║ │ ⚠️ CANCELLATION REASON:                     │ ║ ← Red alert box
║ │ Product out of stock, refund initiated      │ ║
║ ├─────────────────────────────────────────────┤ ║
║ │ [🛒 Continue Shopping]  [📄 View Details]   │ ║ ← Special buttons
║ └─────────────────────────────────────────────┘ ║
╚═════════════════════════════════════════════════╝
```

### Track Order Popup Layout:
```
┌─────────────────────────────────────────────────┐
│ ┌───────────────────────────────────────────┐ │
│ │ 💳 Payment Method: UPI    [PAID Badge]   │ │ ← Purple header
│ └───────────────────────────────────────────┘ │
│                                                 │
│ Order #12345                                    │
│ Placed on Jan 15, 2024        [Delivered]      │
│                                                 │
│ Order ID: #12345    Total: ₹1,499              │
│                                                 │
│ ═══ TIMELINE ═══                                │
│ ●────┬── Order Placed ✓         Jan 15, 10:30  │
│      │                                           │
│ ●────┬── Confirmed ✓            Jan 15, 11:00  │
│      │                                           │
│ ●────┬── Processing ✓           Jan 15, 14:00  │
│      │                                           │
│ ●────┬── Shipped ✓              Jan 16, 09:00  │
│      │                                           │
│ ●────┬── Out for Delivery ✓     Jan 17, 08:00  │
│      │                                           │
│ ●────●── Delivered ✓            Jan 17, 15:30  │
│                                                 │
│ 🎉 Order Successfully Delivered!                │
└─────────────────────────────────────────────────┘
```

### Order Details Popup Layout:
```
┌─────────────────────────────────────────────────┐
│ ┌───────────────────────────────────────────┐ │
│ │ ✓ Payment Status: PAID                    │ │ ← Green banner
│ │ Payment Method: UPI                       │ │
│ └───────────────────────────────────────────┘ │
│                                                 │
│ Order Reference: #12345    |  Date: Jan 15     │
│ Order Status: Delivered    |  Total: ₹1,499    │
│                                                 │
│ ═══ ORDER ITEMS ═══                             │
│ Product Name                    Qty    Price    │
│ ──────────────────────────────────────────────  │
│ Dog Food Premium 10kg           1      ₹999     │
│ Cat Toy Bundle                  2      ₹500     │
│                                                 │
│ ═══ SUMMARY ═══                                 │
│ Subtotal:                              ₹1,499   │
│ Shipping:                              FREE     │
│ Total:                                 ₹1,499   │
└─────────────────────────────────────────────────┘
```

---

## ANIMATION TIMING

```css
Order Card Fade In:     0.5s (sequential)
Badge Pulse:            2s infinite
Shake Animation:        0.5s once
Bounce In (Timeline):   0.6s
Pulse (Active Status):  2s infinite
Slide In Right:         0.5s (staggered by 0.1s)
Tada (Completion):      1s once
```

---

## RESPONSIVE BREAKPOINTS

```css
Desktop:  > 768px  →  Grid 2 columns
Tablet:   ≤ 768px  →  Grid 1 column
Mobile:   < 480px  →  Adjusted padding/font sizes
```

---

## BADGE SPECIFICATIONS

### Status Badge:
- **Padding**: 8px 16px
- **Border Radius**: 20px
- **Font Size**: 11px
- **Font Weight**: 800
- **Text Transform**: UPPERCASE
- **Letter Spacing**: 0.5px

### Payment Badge:
- **Padding**: 8px 14px
- **Border Radius**: 20px
- **Font Size**: 11px
- **Font Weight**: 900
- **Border**: 2px solid
- **Box Shadow**: 0 2px 8px rgba(0,0,0,0.1)
- **Icon Size**: 13px
- **Gap**: 5px

### Cancelled Badge (Order Card):
- **Position**: Absolute top-right (20px, 20px)
- **Padding**: 8px 16px
- **Border Radius**: 25px
- **Font Size**: 11px
- **Font Weight**: 900
- **Background**: Red (#ef4444)
- **Color**: White
- **Box Shadow**: 0 4px 15px rgba(239, 68, 68, 0.4)
- **Animation**: shake 0.5s

---

## BUTTON SPECIFICATIONS

### Standard Action Button:
- **Padding**: 12px 20px
- **Border Radius**: 12px
- **Font Weight**: 800
- **Font Size**: 13px
- **Display**: flex
- **Gap**: 8px
- **Transition**: 0.3s ease
- **Hover Transform**: translateY(-2px)
- **Hover Shadow**: 0 6px 20px rgba(color, 0.4)

### Button States:
```
Default:  Normal gradient
Hover:    Lifted (-2px) + enhanced shadow
Active:   Scale(0.98)
Disabled: Gray (#e2e8f0), no interaction
```

---

## SPACING SYSTEM

```
Container Padding:     40px
Card Padding:          30px
Section Margin:        30px
Element Gap:           15-20px
Tight Gap:             8-12px
```

---

## TYPOGRAPHY

### Font Family:
```
Primary: 'Plus Jakarta Sans', sans-serif
Weights: 400, 600, 700, 800, 900
```

### Font Sizes:
```
Hero Title:      56px (weight 900)
Section Title:   24-28px (weight 900)
Card Title:      18-20px (weight 800)
Body Text:       14-16px (weight 600)
Small Text:      12-13px (weight 600)
Badge Text:      10-11px (weight 800-900)
```

---

## SHADOW SYSTEM

```
Subtle:    0 2px 8px rgba(0,0,0,0.08)
Card:      0 4px 15px rgba(0,0,0,0.08)
Elevated:  0 6px 20px rgba(color,0.4)
Deep:      0 10px 30px rgba(color,0.3)
Dramatic:  0 50px 120px rgba(15, 28, 63, 0.08)
```

---

## BORDER SYSTEM

```
Subtle:       1px solid #eef2f6
Standard:     2px solid #cbd5e1
Emphasis:     2.5px dashed #fde68a
Strong:       3px solid (status color)
Cancelled:    3px solid #ef4444
```

---

## Z-INDEX LAYERS

```
Base:                    0
Card Elements:           1
Badges (absolute):       10
Modals:                  1000
Modal Overlay:           999
```

---

## GRADIENT FORMULAS

### Linear Gradients (135deg):
```
Purple:  #667eea → #764ba2
Green:   #22c55e → #16a34a
Pink:    #f093fb → #f5576c
Blue:    #dbeafe → #e0f2fe
Yellow:  #fef3c7 → #fde68a
Red:     #fee2e2 → #fecaca
```

### Vertical Gradients (to bottom):
```
Cancelled Card:  #fee2e2 → white
Light BG:        #f8fafc → #e2e8f0
```

---

## HOVER EFFECTS

### Cards:
```
Transform:  none (stable)
Shadow:     subtle increase
Border:     color intensify (optional)
```

### Buttons:
```
Transform:  translateY(-2px) scale(1.02)
Shadow:     0 6px 20px rgba(color, 0.4)
Duration:   0.3s ease
```

### Badges:
```
Transform:  none
Animation:  continuous pulse on icon
```

---

## PRINT STYLES

### PDF Receipt:
```css
@media print {
    body { background: #fff; -webkit-print-color-adjust: exact; }
    .no-print { display: none; }
    .receipt-vault { box-shadow: none; border: none; }
    @page { margin: 1cm; }
}
```

---

## ACCESSIBILITY NOTES

- All interactive elements have hover states
- Color contrast meets WCAG AA standards
- Icons paired with text labels
- Font sizes readable (minimum 11px)
- Clear visual hierarchy
- Sufficient spacing for touch targets (minimum 44x44px)

---

**Last Updated**: <?php echo date('d M Y'); ?>  
**Design System Version**: 2.0
