# WooCommerce Bangladesh Hierarchy Checkout & Automation

A lightweight solution for WordPress WooCommerce stores operating in Bangladesh. This script/plugin simplifies the checkout process with a hierarchical location system and automates order notifications.

## 🚀 Features
- **Hierarchical Dropdowns:** Division -> District -> Thana/Upazila selection.
- **Smart Shipping:** Automatically sets delivery charges based on Division (Dhaka vs. Outside Dhaka).
- **Telegram Notifications:** Get real-time order alerts in your Telegram Group.
- **Visitor Tracking:** Get notified when a unique visitor enters your site.
- **No Heavy Plugins:** Works via a few lines of code in `functions.php` or as a standalone plugin.

## 🛠️ Installation

### Option 1: As a Plugin
1. Download the `.zip` file.
2. Upload to `WordPress Dashboard > Plugins > Add New`.
3. Activate and update your Telegram API Token and Chat ID in the code.

### Option 2: Via functions.php
Copy the contents of `wc-bd-checkout-automation.php` and paste them at the end of your theme's `functions.php` file.

## ⚙️ Configuration
Replace the placeholders in the code with your actual credentials:
- `YOUR_BOT_TOKEN`: Your Telegram Bot API token from @BotFather.
- `YOUR_CHAT_ID`: Your Telegram Group Chat ID.

## 📄 License
This project is licensed under the GPL2 License.
