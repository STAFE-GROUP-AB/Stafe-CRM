# Claude Code Setup for Stafe-CRM

This Laravel project has been configured with Claude Code and the following MCP servers:

## Available MCP Servers

### Global Servers (shared across all projects)
1. **GitHub** - Repository access and management
2. **Memory** - Shared knowledge base across projects
3. **Context7** - Latest documentation access
4. **Web Fetch** - External API and resource access

### Project-Specific Servers
1. **Filesystem** - Access to this project's files
2. **Database** - Direct database access for this project
3. **Laravel DebugBar** (if installed) - Debug information

## Usage
1. Open Claude Code in this project directory
2. All MCP servers are automatically configured
3. Use natural language to interact with your codebase
4. Ask Claude to help with Laravel, Livewire, Filament, and Tailwind tasks

## Environment
- Laravel Framework
- Livewire for dynamic components
- Filament for admin interface
- Alpine.js for frontend interactivity
- Tailwind CSS for styling

## Getting Started
Run `source .claude/shortcuts.sh` to load helpful aliases.

## Tips
- Global servers work across all your Laravel projects
- Use project names when referencing files: "Read .env from Stafe-CRM"
- GitHub access works for all your repositories
- Memory is shared, so decisions in one project can inform others

Happy coding! 🚀
