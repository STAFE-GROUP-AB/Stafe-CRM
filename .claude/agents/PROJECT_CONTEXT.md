# Stafe CRM - Project Context for AI Agents

## 🎯 Project Overview

**Stafe CRM** is a comprehensive Customer Relationship Management platform built with Laravel 12 and the TALL Stack. It provides sales teams with tools for contact management, deal pipeline tracking, omni-channel communications, and AI-powered sales intelligence.

## 📊 Tech Stack

| Component | Technology |
|-----------|------------|
| **Backend** | Laravel 12, PHP 8.2+ |
| **Frontend** | Livewire 3, Alpine.js, Tailwind CSS |
| **Database** | MySQL 8+ with multi-tenancy |
| **Auth** | Jetstream with OTP (passwordless) |
| **Testing** | Pest PHP |
| **Code Quality** | Laravel Pint (PSR-12) |
| **Deployment** | Laravel Forge |
| **Communications** | Twilio (VoIP, SMS, WhatsApp) |
| **AI** | OpenAI, Anthropic, Google AI |
| **Email** | Mailgun |

## 🏗️ Architecture Patterns

### Multi-Tenancy
- Team-based data isolation using Jetstream teams
- `team_id` columns for tenant scoping
- Global scopes for automatic filtering

### Service Layer
- Business logic in `app/Services/`
- AI services (AiService, ChatBotService, DealRiskAnalysisService)
- Security services (GdprComplianceService, AuditTrailService, FieldEncryptionService)
- Communication services (TwilioService, TranscriptionService)

### Livewire Components
- Full-page Livewire components for CRUD managers
- Pattern: `ContactManager`, `CompanyManager`, `DealManager`, `TaskManager`
- Modals for create/edit/delete confirmations
- Bulk actions support

### Team Theming
- `TeamTheme` service for per-team color customization
- 9 preset themes (stafe, emerald, blue, indigo, violet, rose, amber, cyan, lime)
- Amber-50 background as default

## 📁 Key Directories

```
app/
├── Http/Controllers/    # Traditional controllers (webhooks, integrations)
├── Livewire/           # 40+ Livewire components
├── Models/             # 50+ Eloquent models
├── Services/           # Business logic services
│   └── Security/       # GDPR, Audit, Encryption services
├── Jobs/               # Background jobs (pending implementation)
└── Events/             # Event classes (pending implementation)

resources/views/
├── livewire/           # Livewire component views
├── components/         # Reusable Blade components
├── layouts/            # App and guest layouts
└── workflows/          # Workflow builder views

database/migrations/    # 115+ migrations
```

## 🔧 Core CRM Features

### Companies
- Model: `App\Models\Company`
- Livewire: `App\Livewire\CompanyManager`
- Fields: name, industry, revenue, employees, address, custom_fields

### Contacts
- Model: `App\Models\Contact`
- Livewire: `App\Livewire\ContactManager`
- Fields: name, email, phone, company_id, social_links, lifetime_value
- Relationships: company, deals, tasks, notes, leadScore, communications

### Deals
- Model: `App\Models\Deal`
- Livewire: `App\Livewire\DealManager`
- Fields: name, value, currency, probability, pipeline_stage_id, status
- Pipeline stages with weighted forecasting

### Tasks
- Model: `App\Models\Task`
- Livewire: `App\Livewire\TaskManager`
- Types: task, call, email, meeting, demo
- Polymorphic: can belong to Contact, Company, or Deal

### Notes
- Model: `App\Models\Note`
- Polymorphic: attached to any entity
- Features: is_private, is_pinned, attachments (JSON)

## 🤖 AI Features

### Lead Scoring
- Service: `App\Services\AiService::calculateLeadScore()`
- Model: `App\Models\LeadScore`
- Factors: engagement, demographics, communication frequency

### Deal Risk Analysis
- Service: `App\Services\DealRiskAnalysisService`
- Risk factors: overdue, stagnation, low activity
- Intervention recommendations

### Price Optimization
- Service: `App\Services\PriceOptimizationService`
- Competitor pricing analysis
- Discount recommendations

### Chatbot
- Service: `App\Services\ChatBotService`
- Live chat with AI responses
- Automatic lead qualification
- Human agent escalation

## 📞 Communications

### Twilio Integration
- Service: `App\Services\TwilioService`
- VoIP calls with recording
- SMS messaging
- WhatsApp integration
- Webhook handlers in `TwilioWebhookController`

### Email
- Templates: `App\Models\EmailTemplate`
- Variable substitution
- Tracking planned (not yet implemented)

## 🔒 Security & Compliance

### GDPR
- Service: `App\Services\Security\GdprComplianceService`
- Consent tracking
- Data access/portability/erasure requests

### Audit Logging
- Service: `App\Services\Security\AuditTrailService`
- Security audit logs with risk levels
- User activity tracking

### Field Encryption
- Service: `App\Services\Security\FieldEncryptionService`
- AES-256-GCM encryption
- Configurable per model/field

### SSO
- Service: `App\Services\Security\SsoService`
- OAuth: Google, Microsoft, GitHub, LinkedIn
- SAML/OIDC: placeholder

## 🎨 UI/UX Standards

### Color Palette
- Background: Amber-50 (light), Gray-900 (dark)
- Primary: Emerald (buttons, links)
- Danger: Red-600 (delete actions)

### Dark Mode
- Uses Tailwind `dark:` prefix
- Some views need dark mode classes (see tasks/work/1.md)

### Components
- Jetstream components (modal, button, input)
- Custom components: data-table, filter-bar, status-badge, empty-state

## 📋 Current Tasks

See `/tasks/work/1.md` for prioritized task list including:
- Dark mode for 10 remaining views
- Export functionality
- Visual workflow builder
- Knowledge base UI completion

## 🌐 Language

- **User-Facing**: English (internationalization ready)
- **Code**: English (variables, comments)
- **Database**: English (column names)

## 🎯 Performance Targets

- Page Load: < 1 second
- Database Queries: < 100ms
- API Response: < 200ms
- Livewire Actions: < 500ms

## ✅ Quality Standards

- Test Coverage: Minimum 80%
- Code Style: PSR-12 (Laravel Pint)
- Type Hints: Required on all functions
- Dark Mode: Required on all views
- Accessibility: WCAG 2.1 AA target
