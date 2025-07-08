# Stafe CRM

A comprehensive, open-source CRM built with the Laravel TALL stack (Tailwind CSS, Alpine.js, Livewire, Laravel). Designed with simplicity and power in mind, following Basecamp's philosophy of clean, intuitive design.

![Dashboard](https://github.com/user-attachments/assets/823dd3bd-e054-4c7b-9bbf-6f14170a8afc)

## Features

### 🏢 **Company Management**
- Complete company profiles with contact information
- Industry classification and employee count tracking
- Annual revenue tracking with multi-currency support
- Custom fields for flexible data collection
- Timezone-aware operations

### 👥 **Contact Management**
- Individual contact profiles with full details
- Company associations and relationship tracking
- Social media links integration
- Lifetime value calculations
- Birthday and bio information
- Custom fields and tagging system

![Contacts](https://github.com/user-attachments/assets/6ebf2179-093d-46c6-8e8b-4e4c8549a47f)

### 💰 **Deal Pipeline**
- Customizable pipeline stages with probability tracking
- Deal value and weighted forecasting
- Expected close date management
- Company and contact associations
- Source tracking and deal types
- Multi-currency support

![Deals](https://github.com/user-attachments/assets/45319311-eea8-4eb7-bbc8-8986563b0349)

### ✅ **Task Management**
- Task creation with multiple types (calls, emails, meetings)
- Priority levels and status tracking
- Due date management with overdue detection
- Polymorphic relationships (tasks can belong to any entity)
- Assignment to team members
- Meeting location and attendee tracking

### 📝 **Notes & Documentation**
- Contextual notes for any entity
- Private and public note visibility
- Pinned important notes
- File attachment support
- Rich content support

### 🏷️ **Flexible Tagging**
- Tag any entity (companies, contacts, deals)
- Color-coded organization
- Powerful filtering and search

### 🔧 **Custom Fields**
- Add custom fields to any entity type
- Multiple field types (text, number, date, select, etc.)
- Validation rules and required fields
- Order customization

## Phase 2 Features

### 📧 **Email Integration**
- Send and receive emails directly from the CRM
- Email templates with variable substitution
- Email tracking (opens, clicks, bounces)
- Automatic email-to-entity linking
- Support for multiple email providers (SMTP, SendGrid, Postmark, etc.)
- Email history and thread management

### 📊 **Advanced Reporting & Analytics**
- Comprehensive dashboard with real-time metrics
- Custom report builder with advanced filtering
- Revenue trends and pipeline analytics
- Performance metrics and KPI tracking
- Scheduled reports via email
- Export reports in multiple formats
- Activity logging and audit trails

### 👥 **Team Collaboration**
- Team management with role-based permissions
- Real-time notifications and mentions
- Commenting system for all entities
- Activity feeds and collaboration history
- Assignment tracking and delegation
- Internal vs. customer-facing communications

### 📁 **Import/Export Functionality**
- Bulk import from CSV/Excel files
- Column mapping and data validation
- Import progress tracking with error handling
- Export data in multiple formats
- Template downloads for proper formatting
- Scheduled and automated exports

### 🔍 **Advanced Search & Filtering**
- Global search across all CRM entities
- Advanced filtering with multiple criteria
- Saved searches for quick access
- Search result highlighting and relevance
- Filter by custom fields and relationships
- Quick filters for common searches

## Technology Stack

- **Laravel 12** - Modern PHP framework
- **Livewire 3.6** - Dynamic, reactive components
- **Alpine.js** - Lightweight JavaScript framework
- **Tailwind CSS 4.0** - Utility-first CSS framework
- **SQLite/MySQL/PostgreSQL** - Database flexibility

## Design Philosophy

Following Basecamp's approach to software design:

- **Simplicity First** - Clean, uncluttered interfaces
- **Progressive Disclosure** - Show what matters, hide complexity
- **Helpful Empty States** - Clear guidance when starting
- **Intuitive Navigation** - Logical organization and flow
- **Mobile Responsive** - Works beautifully on all devices

## Multi-Language & Internationalization

- Multi-language support built-in
- Multi-currency handling for global businesses
- Timezone awareness for distributed teams
- Localized date and number formatting

## Installation

### Requirements

- PHP 8.2+
- Composer
- Node.js & NPM
- Database (SQLite, MySQL, or PostgreSQL)

### Quick Start

```bash
# Clone the repository
git clone https://github.com/STAFE-GROUP-AB/Stafe-CRM.git
cd Stafe-CRM

# Install PHP dependencies
composer install

# Install Node dependencies
npm install

# Create environment file
cp .env.example .env

# Generate application key
php artisan key:generate

# Run migrations and seed default data
php artisan migrate --seed

# Build assets
npm run build

# Start the development server
php artisan serve
```

### Database Setup

The CRM works with SQLite out of the box for quick testing. For production, configure your preferred database in the `.env` file:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=stafe_crm
DB_USERNAME=your_username
DB_PASSWORD=your_password
```

## Architecture

### Database Schema

The CRM uses a flexible, scalable database design:

- **Companies** - Organization data with custom fields
- **Contacts** - People with company relationships
- **Deals** - Sales opportunities with pipeline tracking
- **Tasks** - Activities with polymorphic relationships
- **Notes** - Contextual documentation
- **Tags** - Flexible categorization system
- **Pipeline Stages** - Customizable deal progression
- **Custom Fields** - Extensible field system

### Plugin Architecture

Built with extensibility in mind:
- Modular component structure
- Event-driven architecture
- Custom field system for flexibility
- API-ready for integrations

## API Documentation

RESTful API endpoints for all major entities:
- Companies (`/api/companies`)
- Contacts (`/api/contacts`)
- Deals (`/api/deals`)
- Tasks (`/api/tasks`)
- Notes (`/api/notes`)

GraphQL endpoint available at `/graphql` for complex queries.

## Contributing

We welcome contributions! Please see our [Contributing Guide](CONTRIBUTING.md) for details.

### Development Setup

```bash
# Install dependencies
composer install
npm install

# Set up environment
cp .env.example .env
php artisan key:generate

# Run migrations
php artisan migrate --seed

# Start development server
npm run dev
php artisan serve
```

### Testing

```bash
# Run PHP tests
php artisan test

# Run JavaScript tests
npm test

# Run all tests
composer test
```

## License

This project is open-sourced software licensed under the [MIT License](LICENSE).

## Support

- 📧 Email: support@stafe.com
- 💬 Discord: [Join our community](https://discord.gg/stafe)
- 📖 Documentation: [docs.stafe-crm.com](https://docs.stafe-crm.com)
- 🐛 Issues: [GitHub Issues](https://github.com/STAFE-GROUP-AB/Stafe-CRM/issues)

## Roadmap

### Phase 1 (Current)
- [x] Core CRM entities (Companies, Contacts, Deals, Tasks)
- [x] Dashboard with analytics
- [x] Pipeline management
- [x] Custom fields system
- [x] Tagging system

### Phase 2 (Current Implementation)
- [x] Email integration (send/receive)
- [x] Advanced reporting and analytics
- [x] Team collaboration features
- [x] Import/Export functionality
- [x] Advanced search and filtering

### Phase 3 (Future)
- [ ] Mobile applications (iOS/Android)
- [ ] Advanced automation workflows
- [ ] Integration marketplace
- [ ] Advanced permissions and roles
- [ ] Multi-tenancy support

---

**Built with ❤️ by the Stafe team**
