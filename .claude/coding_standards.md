# Coding Standards for Stafe-CRM

## Laravel Conventions
- Use singular model names (User, Post, not Users, Posts)
- Use plural table names (users, posts)
- Use snake_case for database columns
- Use camelCase for model attributes
- Use PascalCase for class names
- Use Laravel best practices for routes, /app/companies/{company_slug}/{action} and so on
- Always add /app for pages and routes that demands logging in and not for guest routes

## Livewire Best Practices
- Keep components focused and single-purpose
- Use public properties for data binding
- Validate input in the component
- Use lifecycle hooks appropriately
- Emit events for component communication

## Tailwind CSS Guidelines
- Use utility classes over custom CSS
- Follow mobile-first responsive design
- Use consistent spacing scale
- Leverage Tailwind's color palette
- Use component classes for repeated patterns

## Filament Conventions
- Organize resources logically
- Use proper form validation
- Implement proper authorization
- Use custom pages when needed
- Follow Filament's naming conventions
