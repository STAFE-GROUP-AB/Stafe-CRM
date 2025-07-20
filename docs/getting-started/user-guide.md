# Getting Started with Stafe CRM

Welcome to Stafe CRM! This guide will help you get up and running with our comprehensive, open-source CRM platform built with the Laravel TALL stack.

## What is Stafe CRM?

Stafe CRM is a modern, AI-powered customer relationship management platform that combines traditional CRM functionality with advanced automation, machine learning, and intelligent workflows. Designed with simplicity and power in mind, it follows Basecamp's philosophy of clean, intuitive design while providing enterprise-grade features.

## Key Features at a Glance

### 🏢 Core CRM
- **Company & Contact Management** - Complete customer profiles with relationships
- **Deal Pipeline** - Customizable sales pipeline with forecasting
- **Task Management** - Activities with smart assignment and tracking
- **Notes & Documentation** - Contextual notes with file attachments

### 🤖 Advanced Automation
- **Dynamic Content Templates** - Personalized content based on behavior
- **Event-Driven Triggers** - Complex automation responding to real-time events
- **A/B Testing Engine** - Built-in experimentation with statistical analysis
- **Workflow Analytics** - Performance monitoring and optimization insights

### 🎯 AI & Intelligence
- **Smart Lead Scoring** - AI-powered lead qualification and prioritization
- **Predictive Analytics** - ML-based forecasting and churn prediction
- **Conversation Intelligence** - AI analysis of communications and interactions

### 🔗 Integration & Communication
- **Email Integration** - Native email handling with tracking and templates
- **Unified Communications** - Voice, video, SMS, and social media in one platform
- **API-First Design** - Comprehensive REST and GraphQL APIs

## Quick Installation

### Prerequisites

- PHP 8.2 or higher
- Composer
- Node.js & NPM
- Database (SQLite, MySQL, or PostgreSQL)
- Web server (Apache, Nginx, or Laravel Valet)

### Installation Steps

1. **Clone the Repository**
```bash
git clone https://github.com/STAFE-GROUP-AB/Stafe-CRM.git
cd Stafe-CRM
```

2. **Install Dependencies**
```bash
# Install PHP dependencies
composer install

# Install Node.js dependencies
npm install
```

3. **Environment Setup**
```bash
# Create environment file
cp .env.example .env

# Generate application key
php artisan key:generate
```

4. **Database Setup**
```bash
# Run migrations and seed sample data
php artisan migrate --seed
```

5. **Build Assets**
```bash
# Build frontend assets
npm run build
```

6. **Start Development Server**
```bash
# Start the Laravel development server
php artisan serve
```

Your Stafe CRM installation will be available at `http://localhost:8000`.

## Initial Configuration

### 1. Admin Account

After installation, you can access the admin account with:
- **Email**: admin@stafe.com
- **Password**: password

> **Important**: Change the admin password immediately after first login!

### 2. Basic Settings

Navigate to **Settings > System** to configure:

- **Company Information** - Your organization details
- **Email Configuration** - SMTP settings for email integration
- **Currency & Localization** - Default currency and timezone
- **User Permissions** - Role-based access control

### 3. Email Integration

Configure email integration for automated communications:

```env
# SMTP Configuration
MAIL_MAILER=smtp
MAIL_HOST=smtp.your-provider.com
MAIL_PORT=587
MAIL_USERNAME=your-email@domain.com
MAIL_PASSWORD=your-app-password
MAIL_FROM_ADDRESS=crm@your-company.com
MAIL_FROM_NAME="Your Company CRM"
```

### 4. Queue Configuration

For automation features, configure queue processing:

```env
# Queue Configuration
QUEUE_CONNECTION=database

# Or for Redis
QUEUE_CONNECTION=redis
REDIS_HOST=127.0.0.1
REDIS_PASSWORD=null
REDIS_PORT=6379
```

Start the queue worker:
```bash
php artisan queue:work
```

## First Steps

### 1. Import Your Data

Start by importing your existing customer data:

1. Navigate to **Data > Import**
2. Download the CSV template for contacts or companies
3. Prepare your data using the template format
4. Upload and map your fields
5. Review and confirm the import

### 2. Set Up Your Pipeline

Configure your sales pipeline:

1. Go to **Settings > Pipeline Stages**
2. Create stages that match your sales process
3. Set probability percentages for forecasting
4. Configure stage-specific automation rules

### 3. Create User Accounts

Add your team members:

1. Navigate to **Users > Add User**
2. Set appropriate roles and permissions
3. Assign users to teams if using team-based features
4. Send invitation emails to new users

### 4. Configure Automation

Set up basic automation workflows:

1. **Welcome Email** - Automatically send welcome emails to new contacts
2. **Lead Assignment** - Route leads to appropriate sales reps
3. **Follow-up Reminders** - Create tasks for stalled deals
4. **Activity Tracking** - Log interactions automatically

## Understanding the Interface

### Dashboard

The main dashboard provides:
- **Key Metrics** - Pipeline value, conversion rates, activity summary
- **Recent Activity** - Latest interactions and updates
- **Quick Actions** - Fast access to common tasks
- **Upcoming Tasks** - Scheduled activities and deadlines

### Navigation Structure

- **Contacts** - Individual contact management
- **Companies** - Organization profiles and relationships
- **Deals** - Sales opportunities and pipeline
- **Tasks** - Activities and to-do items
- **Analytics** - Reports and performance insights
- **Automation** - Workflows, triggers, and templates
- **Settings** - Configuration and administration

### Search & Filtering

Use the global search to find:
- Contacts by name, email, or phone
- Companies by name or domain
- Deals by title or value
- Notes and activity content

Advanced filters allow you to:
- Filter by multiple criteria
- Save frequently used searches
- Create dynamic lists and segments

## Core Workflows

### 1. Adding a New Contact

```
1. Click "New Contact" from any page
2. Enter basic information (name, email, phone)
3. Associate with a company (create if new)
4. Add tags and custom field data
5. Set initial status and assignment
6. Save and continue with next actions
```

### 2. Creating a Deal

```
1. Navigate to Deals > New Deal
2. Enter deal details (title, value, stage)
3. Associate with contact and company
4. Set expected close date and probability
5. Add notes about the opportunity
6. Assign to appropriate sales rep
```

### 3. Setting Up Automation

```
1. Go to Automation > Workflows
2. Choose a trigger (new contact, deal stage change, etc.)
3. Define conditions for when the workflow runs
4. Add actions (send email, create task, update field)
5. Test the workflow with sample data
6. Activate and monitor performance
```

## Best Practices

### Data Management
- **Consistent Naming** - Use standardized naming conventions
- **Regular Cleanup** - Remove duplicates and outdated information
- **Field Standards** - Establish rules for custom fields and tags
- **Backup Strategy** - Regular data backups and export procedures

### Automation Strategy
- **Start Simple** - Begin with basic workflows before complex automation
- **Test Thoroughly** - Always test automation with sample data first
- **Monitor Performance** - Regularly review automation analytics
- **Iterate & Improve** - Continuously optimize based on results

### Team Adoption
- **Training Program** - Systematic training for all team members
- **Clear Processes** - Document workflows and procedures
- **Regular Reviews** - Weekly team reviews of CRM usage
- **Feedback Loop** - Collect and act on user feedback

## Common Use Cases

### Sales Team
- Track prospects through the sales pipeline
- Automate follow-up sequences
- Forecast revenue and analyze win rates
- Collaborate on deals with team notes

### Marketing Team
- Segment contacts for targeted campaigns
- Track campaign performance and ROI
- Score leads based on engagement
- Nurture prospects with automated sequences

### Customer Success
- Monitor customer health scores
- Automate onboarding processes
- Track support interactions
- Identify expansion opportunities

### Management
- View real-time performance dashboards
- Generate executive reports
- Analyze team productivity
- Make data-driven decisions

## Getting Help

### Documentation
- **Feature Guides** - Detailed documentation for each feature
- **API Reference** - Complete API documentation for developers
- **Video Tutorials** - Step-by-step video guides
- **Best Practices** - Recommended approaches and tips

### Community Support
- **GitHub Issues** - Bug reports and feature requests
- **Discussions** - Community Q&A and knowledge sharing
- **Contributing** - Guidelines for contributing to the project

### Professional Support
- **Email Support** - support@stafe.com
- **Training Services** - Custom training for your team
- **Consulting** - Implementation and optimization consulting
- **Development Services** - Custom feature development

## Next Steps

Now that you have Stafe CRM installed and configured:

1. **Explore the Features** - Take time to explore each module
2. **Import Your Data** - Bring in your existing contacts and deals
3. **Set Up Automation** - Start with simple workflows
4. **Train Your Team** - Ensure everyone knows how to use the system
5. **Monitor & Optimize** - Use analytics to continuously improve

### Recommended Reading

- [Dynamic Content Templates](../automation/dynamic-content-templates.md) - Create personalized communications
- [Event-Driven Triggers](../automation/event-driven-triggers.md) - Set up intelligent automation
- [A/B Testing Engine](../automation/ab-testing-engine.md) - Optimize your processes with testing
- [Workflow Analytics](../automation/workflow-analytics.md) - Monitor and improve performance

---

Welcome to the future of customer relationship management! Stafe CRM combines the simplicity of modern design with the power of AI and automation to help your team build better customer relationships and grow your business.