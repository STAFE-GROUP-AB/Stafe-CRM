# Stafe CRM

A comprehensive, open-source CRM built with the Laravel TALL stack (Tailwind CSS, Alpine.js, Livewire, Laravel). Designed with simplicity and power in mind, following Basecamp's philosophy of clean, intuitive design.

![Dashboard](https://github.com/user-attachments/assets/823dd3bd-e054-4c7b-9bbf-6f14170a8afc)

## Features

### Phase 1 - Core CRM Functionality

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

## Phase 2 - Advanced CRM Features

### 📧 **Email Integration**
- **Send & Receive**: Direct email integration with CRM entities
- **Smart Templates**: Dynamic email templates with variable substitution
- **Email Tracking**: Monitor opens, clicks, bounces, and delivery status
- **Multi-Provider**: Support for SMTP, SendGrid, Postmark, AWS SES
- **Thread Management**: Complete email history and conversation tracking
- **Auto-Linking**: Automatic email-to-contact/deal association

### 📊 **Advanced Reporting & Analytics**
- **Real-time Dashboard**: Live metrics and KPI monitoring
- **Custom Report Builder**: Drag-and-drop report creation with advanced filtering
- **Revenue Analytics**: Pipeline analysis, win rates, and forecasting
- **Activity Insights**: Comprehensive audit trails and user activity tracking
- **Scheduled Reports**: Automated report generation and email delivery
- **Export Options**: Multiple format support (PDF, Excel, CSV)
- **Performance Metrics**: Team performance and individual productivity tracking

### 👥 **Team Collaboration**
- **Team Management**: Organize users into teams with role-based permissions
- **Real-time Notifications**: Instant alerts for assignments, mentions, and deadlines
- **Smart Comments**: Entity-based discussions with @mentions and threading
- **Activity Feeds**: Collaborative timeline showing all team interactions
- **Permission Control**: Granular access control for teams and individuals
- **Internal Communication**: Separate internal notes from customer-facing content

### 📁 **Import/Export Functionality**
- **Bulk Import**: CSV/Excel import with intelligent field mapping
- **Data Validation**: Advanced validation with detailed error reporting
- **Progress Tracking**: Real-time import status with success/failure metrics
- **Template Downloads**: Pre-configured templates for easy data preparation
- **Error Recovery**: Detailed error logs with suggestions for data fixes
- **Scheduled Exports**: Automated data exports for backup and integration

### 🔍 **Advanced Search & Filtering**
- **Global Search**: Lightning-fast search across all CRM entities
- **Smart Filters**: Complex multi-criteria filtering with logical operators
- **Saved Searches**: Store frequently used search configurations
- **Quick Filters**: One-click filters for common search patterns
- **Search Highlighting**: Visual emphasis on search matches
- **Cross-Entity Search**: Find related data across companies, contacts, and deals

## Phase 3 - Enterprise Features

### 🔄 **Advanced Automation Workflows**
- **Workflow Builder**: Create multi-step automation workflows with visual builder
- **Smart Triggers**: Event-based, scheduled, and manual workflow triggers
- **Action Engine**: Send emails, create tasks, update fields, and custom actions
- **Conditional Logic**: Advanced conditions and branching for complex workflows
- **Status Monitoring**: Real-time workflow execution tracking and error handling
- **Template Library**: Reusable workflow templates for common automation patterns

### 🔗 **Integration Marketplace**
- **Curated Marketplace**: Browse and install integrations from organized categories
- **Popular Integrations**: Mailchimp, Slack, Shopify, Google Analytics, Trello, and more
- **OAuth & API Keys**: Secure authentication with multiple auth methods
- **Real-time Sync**: Webhook and API-based data synchronization
- **Connection Management**: Test, monitor, and manage external service connections
- **Custom Integrations**: Support for custom integrations and API endpoints

### 🔐 **Advanced Permissions & Roles**
- **Granular Permissions**: Detailed permission system with 35+ individual permissions
- **Custom Roles**: Create unlimited custom roles with specific permission sets
- **Role Categories**: Organized permissions by functional areas (CRM, Admin, Reporting)
- **Scoped Access**: Team or entity-specific permission assignments
- **System Protection**: Protected system roles and permissions
- **Permission Inheritance**: Hierarchical permission structure for complex organizations

### 🏢 **Multi-Tenancy Support**
- **Complete Isolation**: Full data separation between tenant organizations
- **Custom Domains**: Support for custom subdomains and domains per tenant
- **Tenant Settings**: Customizable settings and configurations per organization
- **Feature Management**: Enable/disable features per tenant subscription
- **Resource Limits**: User count and storage limits with usage tracking
- **Trial Management**: Built-in trial period and subscription management

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

### Phase 2 Configuration

#### Email Integration Setup
```env
# Email Configuration
MAIL_MAILER=smtp
MAIL_HOST=smtp.your-provider.com
MAIL_PORT=587
MAIL_USERNAME=your-email@domain.com
MAIL_PASSWORD=your-app-password
MAIL_FROM_ADDRESS=crm@your-company.com
MAIL_FROM_NAME="Your Company CRM"

# For SendGrid
MAIL_MAILER=sendgrid
SENDGRID_API_KEY=your-sendgrid-api-key

# For Postmark
MAIL_MAILER=postmark
POSTMARK_TOKEN=your-postmark-token
```

#### Queue Configuration (for Import/Export)
```env
QUEUE_CONNECTION=database
# Or for Redis
QUEUE_CONNECTION=redis
REDIS_HOST=127.0.0.1
REDIS_PASSWORD=null
REDIS_PORT=6379
```

#### File Storage (for imports/exports)
```env
FILESYSTEM_DISK=local
# Or for S3
FILESYSTEM_DISK=s3
AWS_ACCESS_KEY_ID=your-access-key
AWS_SECRET_ACCESS_KEY=your-secret-key
AWS_DEFAULT_REGION=us-east-1
AWS_BUCKET=your-bucket-name
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

### Core API Endpoints

RESTful API endpoints for all major entities:

**Core Entities:**
- Companies (`/api/companies`)
- Contacts (`/api/contacts`)
- Deals (`/api/deals`)
- Tasks (`/api/tasks`)
- Notes (`/api/notes`)
- Tags (`/api/tags`)

**Phase 2 Endpoints:**
- Emails (`/api/emails`)
- Email Templates (`/api/email-templates`)
- Teams (`/api/teams`)
- Reports (`/api/reports`)
- Notifications (`/api/notifications`)
- Import Jobs (`/api/import-jobs`)
- Saved Searches (`/api/saved-searches`)

### Advanced Features API

**Email Integration:**
```
POST /api/emails/send
GET /api/emails/{id}/tracking
POST /api/email-templates
```

**Reporting & Analytics:**
```
POST /api/reports/generate
GET /api/analytics/dashboard
GET /api/analytics/revenue-trends
```

**Team Collaboration:**
```
POST /api/teams/{id}/members
POST /api/comments
GET /api/notifications/unread
```

**Import/Export:**
```
POST /api/import/contacts
GET /api/import/{id}/status
POST /api/export/deals
```

GraphQL endpoint available at `/graphql` for complex queries and real-time subscriptions.

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

### Phase 3 (Completed)
- [ ] Mobile applications (iOS/Android) - *Skipped per requirements*
- [x] Advanced automation workflows
- [x] Integration marketplace
- [x] Advanced permissions and roles
- [x] Multi-tenancy support

---

**Built with ❤️ by the Stafe team**
