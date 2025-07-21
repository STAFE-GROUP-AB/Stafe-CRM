# Claude Instructions for Stafe-CRM Laravel Project

## Project Context & Tech Stack
You are working with a Laravel full-stack developer on the "Stafe-CRM" project. This is a Laravel application using:

- **Framework**: Laravel (latest version)
- **Frontend Stack**: Livewire + Alpine.js + Tailwind CSS
- **Admin Interface**: Filament
- **Database**: mysql
- **Development Focus**: Full-stack Laravel development with modern frontend tools

## Developer Preferences & Coding Style

### Laravel Best Practices
- Always follow Laravel conventions and best practices
- Use Eloquent ORM for database operations
- Implement proper request validation using Form Requests
- Use Laravel's built-in authentication and authorization
- Follow PSR-12 coding standards
- Use meaningful variable and method names
- Write comprehensive feature tests

### Livewire Development
- Prefer Livewire over Vue/React for dynamic components
- Use public properties for data binding
- Implement proper validation in Livewire components
- Use lifecycle hooks appropriately (mount, render, updated, etc.)
- Emit events for component communication
- Keep components focused and single-purpose
- Use wire:model for form inputs
- Implement real-time validation with wire:model.lazy or wire:model.debounce

### Filament Administration
- Use Filament for all admin interfaces
- Create proper Resource classes for models
- Implement custom pages when needed
- Use Filament's form builder for complex forms
- Leverage Filament's table builder for listings
- Implement proper authorization policies
- Use Filament's notification system
- Create custom widgets for dashboards

### Frontend Development
- Use Tailwind CSS utility classes exclusively
- Prefer utility classes over custom CSS
- Follow mobile-first responsive design principles
- Use Alpine.js for simple client-side interactivity
- Keep Alpine.js components small and focused
- Use Tailwind's design system (spacing, colors, typography)
- Implement dark mode support when requested

### Database & Models
- Use migrations for all database changes
- Create proper model relationships
- Use factories for testing data
- Implement model scopes for reusable queries
- Use accessors and mutators appropriately
- Follow Laravel's naming conventions for tables and columns

## Available Tools
You have access to the following MCP servers:
- **Context7**: Access latest Laravel documentation and any other framework docs
- **Filesystem**: Read and edit project files
- **Database**: Query and modify database directly
- **Memory**: Remember project decisions and patterns
- **GitHub**: Manage repository operations
- **Web Fetch**: Access external resources
- **Figma**: Access Figma designs, components, and design tokens (if configured)

Use these tools actively to understand the project structure, run commands, and maintain context across sessions.

## Figma Integration
If Figma is configured, you can:
- Access design files and components
- Extract design tokens (colors, typography, spacing)
- Get component specifications for implementation
- Sync design system changes with your Laravel/Livewire/Tailwind components

## Project-Specific Notes
- Database connection: mysql
- Project started: $(date)
- Initial setup completed with full MCP server configuration
- Figma integration: Available if token was provided

Remember: Always prioritize Laravel conventions, use the developer's preferred stack (Livewire/Filament/Alpine/Tailwind), and maintain high code quality standards.
