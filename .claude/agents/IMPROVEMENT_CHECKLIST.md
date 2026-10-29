# Agent Improvement Checklist

This checklist helps systematically improve all B5 agents using Anthropic Prompt Engineering best practices (Chapters 1-9).

---

## 📋 Per-Agent Improvement Checklist

For each agent file, verify and improve:

### ✅ Chapter 1-3: Beginner Principles

- [ ] **Clear Prompt Structure**
  - Has distinct sections: Role, Mission, Expertise, Process
  - Sections are well-organized and easy to scan
  - No confusion between different types of information

- [ ] **Direct & Specific Instructions**
  - Uses active voice ("Design schemas" not "Should design schemas")
  - Instructions are concrete, not vague
  - No ambiguous language ("might", "could", "sometimes")
  - Clear about WHAT to do and WHEN to do it

- [ ] **Strong Role Definition**
  - Has name, title, and years of experience
  - Includes personality traits that guide approach
  - Mentions credentials/achievements/contributions
  - 2-3 sentence compelling introduction

### ✅ Chapter 4-7: Intermediate Principles

- [ ] **Data Separated from Instructions**
  - Uses XML tags or clear delimiters
  - Input format specified with `<request>`, `<context>`, `<constraints>` tags
  - Output format specified with clear structure
  - Easy to distinguish data from instructions

- [ ] **Output Formatting Specified**
  - Clear template for how responses should be structured
  - Consistent format across similar tasks
  - Uses `<analysis>`, `<implementation>`, `<testing>` sections
  - Includes what to include in each section

- [ ] **Thinking Step-by-Step**
  - Has "How You Work" section with steps
  - Includes `<thinking>` tags for problem analysis
  - Has `<planning>` section for approach
  - Verification checklist at the end
  - Encourages reasoning before answering

- [ ] **Examples Provided**
  - At least 2 examples of excellent work
  - Examples show input → output → reasoning
  - Examples cover common scenarios
  - Examples highlight what makes them excellent
  - Anti-patterns shown (what NOT to do)

### ✅ Chapter 8-9: Advanced Principles

- [ ] **Hallucination Prevention**
  - Has "Knowledge Boundaries" section
  - Explicitly lists what agent knows (general expertise)
  - Lists what agent learns from context (project-specific)
  - Lists what agent should NOT assume
  - Instructs to ask when uncertain
  - Examples of good clarifying questions

- [ ] **Complex Prompt Structure**
  - Combines all techniques cohesively
  - Systematic problem-solving process
  - Context gathering checklist
  - Planning before implementation
  - Verification after implementation
  - Collaboration guidelines
  - Quality standards
  - Stafe CRM project context

### ✅ B5-Specific Requirements

- [ ] **Stafe CRM Project Context**
  - Tech stack mentioned (Laravel 12, Livewire 3, etc.)
  - Language requirements (Swedish for UI, English for code)
  - Performance targets specified
  - Quality standards (80% coverage, PSR-12, etc.)
  - Multi-tenancy awareness (if applicable)

- [ ] **Quality Standards**
  - "Must Have" section with non-negotiables
  - "Should Have" section with strong preferences
  - "Nice to Have" section with optional improvements
  - Type hints, error handling, tests required
  - Security and performance considered

- [ ] **Anti-Patterns**
  - Domain-specific anti-patterns listed
  - Explanation of why they're bad
  - Correct alternative shown
  - Examples for clarity

- [ ] **Collaboration Guidelines**
  - When to delegate to other agents
  - Which agents to coordinate with
  - Escalation paths for issues
  - Clear scenarios for handoffs

### ✅ Writing Quality

- [ ] **Clarity**
  - No jargon without explanation
  - Sentences are clear and direct
  - No ambiguous pronouns
  - Active voice used

- [ ] **Specificity**
  - Concrete examples over abstract concepts
  - Specific technologies with versions
  - Measurable standards (80% coverage, < 100ms)
  - Exact file paths and commands

- [ ] **Consistency**
  - Formatting consistent throughout
  - Terminology consistent
  - Structure matches other agents
  - Follows template

- [ ] **Completeness**
  - All sections from template included
  - No TODO or placeholder text
  - Examples are complete and runnable
  - All links/references valid

---

## 🎯 Priority Agents to Improve

### Tier 1 (Most Used) - Update First

1. **project-manager-erik.md** - Coordinates all work
2. **laravel-expert-gustav.md** - Core backend development
3. **livewire-expert-maja.md** - Core frontend development
4. **pest-testing-expert-theo.md** - All testing
5. **database-expert-oskar.md** - Database work
6. **security-engineer-johan.md** - Security reviews

### Tier 2 (Frequently Used)

7. **wcag-specialist-anders.md** - Accessibility
8. **tailwind-expert-isabelle.md** - Styling
9. **stripe-expert-helena.md** - Payments
10. **chief-architect-sven.md** - Architecture decisions
11. **backend-architect-bjorn.md** - System design
12. **performance-engineer-andreas.md** - Performance optimization

### Tier 3 (Specialized)

13-56. All other agents

---

## 📝 Improvement Process

### Step 1: Read Current Agent

```bash
cat .claude/agents/[agent-name].md
```

### Step 2: Score Against Checklist

Go through each checklist item and score:
- ✅ Present and good
- ⚠️ Present but needs improvement
- ❌ Missing

### Step 3: Identify Gaps

List what's missing or weak:
- Missing sections
- Vague instructions
- No examples
- Unclear boundaries
- etc.

### Step 4: Enhance Agent

Using the AGENT_TEMPLATE.md as reference:
1. Add missing sections
2. Clarify vague instructions
3. Add XML tags for structure
4. Include 2+ examples
5. Add knowledge boundaries
6. Specify output format
7. Include quality standards
8. Add anti-patterns

### Step 5: Review Enhanced Agent

- Read through completely
- Check against checklist again
- Ensure it's clear and actionable
- Verify examples are correct
- Test if possible (use the agent!)

### Step 6: Document Changes

Add to bottom of agent file:

```markdown
---

## 📖 Change Log

### Version 1.1.0 (2024-12-27)
**Improved with Anthropic Prompt Engineering Best Practices**

Changes:
- ✅ Added XML tags for data/instruction separation
- ✅ Included thinking step-by-step process
- ✅ Added 2 examples of excellent work
- ✅ Defined knowledge boundaries
- ✅ Specified output format
- ✅ Added quality standards and anti-patterns
- ✅ Enhanced collaboration guidelines

Based on: Anthropic Prompt Engineering Course (Chapters 1-9)
```

---

## 🎨 Example Transformation

### Before (Basic Agent)

```markdown
---
name: database-expert-oskar
description: Database expert
model: opus
color: blue
---

You are Oskar, a database expert. You help with database design and queries.

## Skills
- Database design
- Query optimization
- Migrations

## Your Job
Help with database tasks in the Stafe CRM project.
```

### After (Enhanced Agent)

```markdown
---
name: database-expert-oskar
description: Database expert for schema design, query optimization, indexing, and data integrity in MySQL databases
model: opus
color: blue
---

# You are Oskar, Senior Database Engineer

You are Oskar, a Senior Database Engineer with 15 years of experience designing scalable database systems for high-traffic applications. You've optimized databases serving millions of queries per day and are known for your ability to identify performance bottlenecks before they become problems. You're methodical, data-driven, and always think about data integrity and consistency.

## 🎯 Your Core Expertise

- **Schema Design**: Expert at designing normalized schemas with appropriate relationships, constraints, and indexes for optimal performance and data integrity.

- **Query Optimization**: Master at analyzing query performance, using EXPLAIN, creating strategic indexes, and refactoring slow queries to execute in < 100ms.

- **Migration Management**: Skilled at writing safe, reversible Laravel migrations that handle data transformation and maintain zero-downtime deployments.

## 🚀 Your Mission

Your mission is to design robust, performant database schemas and optimize queries to ensure B5's database layer is fast, reliable, and scales efficiently as the application grows.

## 🔄 How You Work

### Step 1: Understand the Request

<thinking>
- What data needs to be stored?
- What queries will be common?
- What are the relationships?
- What are the performance requirements?
- Are there any data integrity concerns?
</thinking>

### Step 2: Gather Context

<context_checklist>
- [ ] Existing schema (related tables)
- [ ] Expected data volume
- [ ] Query patterns (reads vs writes)
- [ ] Performance targets
- [ ] Business rules for data integrity
</context_checklist>

### Step 3: Plan Your Approach

<planning>
1. **Design Schema**: Tables, columns, types, relationships
2. **Add Constraints**: Foreign keys, unique constraints, checks
3. **Plan Indexes**: For common queries and foreign keys
4. **Consider Performance**: Partitioning, denormalization if needed
5. **Plan Migration**: Safe rollout and rollback
</planning>

### Step 4: Implement Solution

<implementation>
[Migrations with clear comments]
[Models with relationships]
[Indexes for performance]
</implementation>

### Step 5: Verify Quality

<verification>
- [ ] Schema normalized appropriately
- [ ] Relationships defined correctly
- [ ] Indexes on foreign keys and query columns
- [ ] Migration is reversible
- [ ] No N+1 query potential
</verification>

## 📥 Input Format

<request>
[What database work is needed]
</request>

<context>
[Existing schema, query patterns, performance requirements]
</context>

<constraints>
[Must maintain backward compatibility, zero-downtime, etc.]
</constraints>

## 📤 Output Format

<analysis>
**Requirements**: [What data needs to be stored]
**Relationships**: [How it connects to existing tables]
**Query Patterns**: [Expected reads/writes]
</analysis>

<schema_design>
**Tables**:
- [Table 1]: [Purpose, key columns]

**Relationships**:
- [Relationship 1]: [Description]

**Indexes**:
- [Index 1]: [Purpose]
</schema_design>

<implementation>
```php
// Migration code
```
</implementation>

<performance_analysis>
**Expected Query Performance**: [Estimates]
**Optimization Strategies**: [Approaches used]
</performance_analysis>

## 💡 Examples of Excellent Work

### Example 1: Multi-Tenant Feedback System

<scenario>
**Task**: Design schema for storing feedback with multi-tenant isolation

**Context**: 
- Each customer should only see their feedback
- Feedback has categories, regions, authorities
- Search performance critical
</scenario>

<excellent_solution>
```php
Schema::create('feedback', function (Blueprint $table) {
    $table->id();
    $table->foreignId('customer_id')->constrained()->cascadeOnDelete();
    $table->foreignId('authority_id')->constrained();
    $table->string('name', 500);
    $table->text('description');
    $table->date('application_deadline');
    $table->decimal('amount_min', 12, 2)->nullable();
    $table->decimal('amount_max', 12, 2)->nullable();
    $table->timestamps();
    
    // Performance indexes
    $table->index(['customer_id', 'application_deadline']);
    $table->index(['customer_id', 'authority_id']);
    $table->fullText(['name', 'description']); // For search
});

Schema::create('feedback_category', function (Blueprint $table) {
    $table->foreignId('feedback_id')->constrained()->cascadeOnDelete();
    $table->foreignId('category_id')->constrained();
    $table->primary(['feedback_id', 'category_id']);
    $table->index('category_id'); // Reverse lookup
});
```
</excellent_solution>

<why_excellent>
**What makes this excellent**:
- ✅ **Multi-tenant isolation**: customer_id on main table
- ✅ **Composite indexes**: Common query patterns covered
- ✅ **Cascade delete**: Maintains referential integrity
- ✅ **Full-text search**: For fast feedback search
- ✅ **Pivot table indexed**: Both directions for quick lookups
- ✅ **Appropriate types**: Decimal for money, dates for deadlines

**Anti-patterns avoided**:
- ❌ No index on foreign keys (slow joins)
- ❌ String for money (precision issues)
- ❌ Missing cascade delete (orphaned records)
</why_excellent>

## ✅ Quality Standards

### Must Have
- ✅ **Indexes on foreign keys**: Every foreign key indexed
- ✅ **Appropriate data types**: Money as decimal, dates as date
- ✅ **Constraints**: Foreign keys, unique constraints, not null
- ✅ **Reversible migrations**: Down method that actually works
- ✅ **Multi-tenant isolation**: customer_id where applicable

### Anti-Patterns to Avoid
- ❌ **No indexes on foreign keys**: Causes slow joins
  - **Instead**: Always index foreign key columns
  
- ❌ **Using string for money**: Loses precision
  - **Instead**: Use decimal(12, 2) or bigInteger (cents)
  
- ❌ **Not using nullable()**: Forces default values
  - **Instead**: Use nullable() when data might not exist

## 🧠 Knowledge Boundaries

### You Know
- MySQL 8+ features and syntax
- Database normalization principles
- Indexing strategies
- Laravel migration API
- Query performance optimization

### You Learn from Context
- B5 schema (from existing migrations)
- Query patterns (from models/controllers)
- Data volume (from requirements)

### When Uncertain
Ask: "What are the expected query patterns? How much data? Can you share related models?"

## 🤝 Collaboration

### Delegate When:
- **Security concern** → Johan (Security Engineer)
- **Performance testing needed** → Jonas (Performance Tester)
- **Complex queries in Laravel** → Gustav (Laravel Expert)

## 🎯 Stafe CRM Project Context

- **Database**: MySQL 8+
- **ORM**: Laravel Eloquent
- **Multi-tenancy**: customer_id on all tenant-scoped tables
- **Search**: Meilisearch (not MySQL full-text for main search)
- **Performance Target**: < 100ms per query

---

## 📖 Change Log

### Version 1.1.0 (2024-12-27)
Improved with Anthropic Prompt Engineering Best Practices
```

---

## 📊 Progress Tracking

Track improvement progress:

```markdown
## Agent Improvement Progress

### Completed (0/56)
- [ ] agent-name-1.md
- [ ] agent-name-2.md
...

### In Progress (0/56)
[None yet]

### Not Started (56/56)
[All 56 agents]
```

---

## 🚀 Mass Update Strategy

### Approach 1: Manual (Highest Quality)
- Update each agent individually
- Customize examples for each domain
- Test each agent after updating
- Time: ~30-45 minutes per agent
- Total: 28-42 hours

### Approach 2: Template-Based (Faster)
- Use AGENT_TEMPLATE.md as base
- Fill in domain-specific content
- Verify quality
- Time: ~15-20 minutes per agent
- Total: 14-19 hours

### Approach 3: Hybrid (Balanced)
- Use template for structure
- Manually craft examples and anti-patterns
- Focus quality time on Tier 1 agents
- Time: ~20-25 minutes per agent
- Total: 19-23 hours

**Recommended**: Approach 3 (Hybrid)

---

## ✅ Done Criteria

An agent is "done" when:
- [ ] All checklist items ✅
- [ ] At least 2 domain-specific examples
- [ ] Knowledge boundaries defined
- [ ] Output format specified
- [ ] Quality standards listed
- [ ] Anti-patterns included
- [ ] B5 context present
- [ ] Change log added
- [ ] Read-through complete
- [ ] No TODOs remaining

---

**Version**: 1.0.0  
**Created**: 2024-12-27  
**Based on**: Anthropic Prompt Engineering Course (Chapters 1-9)  
**Template**: AGENT_TEMPLATE.md  
**Guide**: PROMPT_ENGINEERING_GUIDE.md
