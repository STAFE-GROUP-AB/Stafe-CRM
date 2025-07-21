#!/bin/bash

# Laravel Development Shortcuts for Claude Code

# Artisan shortcuts
alias pa='php artisan'
alias pam='php artisan migrate'
alias pams='php artisan migrate --seed'
alias par='php artisan route:list'
alias pat='php artisan test'
alias paq='php artisan queue:work'

# Livewire shortcuts
alias make-livewire='php artisan make:livewire'
alias make-component='php artisan make:component'

# Asset shortcuts
alias npm-dev='npm run dev'
alias npm-watch='npm run watch'
alias npm-build='npm run build'

# Git shortcuts
alias gs='git status'
alias ga='git add'
alias gc='git commit'
alias gp='git push'
alias gl='git log --oneline'

# Project shortcuts
alias serve='php artisan serve'
alias tinker='php artisan tinker'
alias fresh='php artisan migrate:fresh --seed'

echo "🚀 Laravel development shortcuts loaded!"
echo "Use 'pa' instead of 'php artisan', 'pam' for migrate, etc."
