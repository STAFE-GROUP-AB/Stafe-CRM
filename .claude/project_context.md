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

## 🎨 Figma MCP Usage Guide

### ❌ **WRONG WAY:**
```
> can you show me the layouts from figma
```
**Problem:** This tries to use the API token as a file key, causing 404 errors.

### ✅ **CORRECT WAY:**
```
> can you analyze this figma file: https://www.figma.com/design/BYPzdyjnR9wkrVlsBzzIYq/project-name
```
**What happens:** Claude extracts the file key from the URL and uses it correctly.

### How to Request Figma Information

1. **Full Figma URL (Recommended):**
   ```
   > Please analyze this Figma design: https://www.figma.com/design/FILE_KEY/Project-Name
   ```

2. **Specific Frame/Component:**
   ```
   > Analyze this specific frame: https://www.figma.com/design/FILE_KEY/Project?node-id=0-1
   ```

3. **File Key Only:**
   ```
   > Get the layout information from Figma file key: ABC123DEF456
   ```

### Understanding File Keys vs API Tokens

**API Token (for authentication):**
- Format: `figd_xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx`
- Purpose: Authenticates requests to Figma API
- Already configured in your MCP server

**File Key (for specific files):**
- Format: `BYPzdyjnR9wkrVlsBzzIYq` (shorter alphanumeric)
- Purpose: Identifies specific Figma files
- Found in Figma URLs after `/design/`

### Best Practices for Figma Integration

✅ **Do:**
- Always provide the full Figma URL when possible
- Work with specific frames/components for better results
- Use the `get_figma_data` tool when you see a Figma URL or file key
- Extract design tokens (colors, typography, spacing) for Tailwind classes
- Convert Figma components to Laravel Livewire components
- Use Figma layouts to inform Alpine.js interactions

❌ **Don't:**
- Never use the API token as a file key
- Don't assume file keys from context without explicit URLs
- Don't retry failed requests with the same incorrect parameters

### Laravel + Figma Workflow

When working with Figma designs in this Laravel project:

1. **Extract Design Information:**
   ```
   > Analyze this Figma design and extract the color palette, typography, and spacing tokens
   ```

2. **Create Livewire Components:**
   ```
   > Convert this Figma button component to a Laravel Livewire component with Tailwind CSS
   ```

3. **Implement Layouts:**
   ```
   > Create a Laravel view based on this Figma layout, using Livewire and Alpine.js
   ```

4. **Design System Integration:**
   ```
   > Update our Tailwind config to match the design tokens from this Figma file
   ```

### Error Handling

**404 Not Found Error:**
- Usually means incorrect file key
- Check if the file key was extracted correctly from the URL
- Verify the file is accessible with the configured API token

**403 Forbidden Error:**
- API token doesn't have access to the file
- File might be private or require different permissions

### Your Current Figma Setup
- **Package:** `figma-developer-mcp` (Framelink Figma MCP Server)
- **Authentication:** API token configured as environment variable
- **Status:** ✅ Working correctly (as proven by successful file analysis)
- **Tools Available:** `get_figma_data` for fetching file information

Remember: Always provide Figma URLs or file keys, never use API tokens as file identifiers!

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
