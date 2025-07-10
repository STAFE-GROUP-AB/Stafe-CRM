<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\SalesContentCategory;
use App\Models\Achievement;
use App\Models\BattleCard;
use App\Models\SalesPlaybook;
use App\Models\PlaybookStep;

class SalesEnablementSeeder extends Seeder
{
    public function run(): void
    {
        // Create Sales Content Categories
        $categories = [
            [
                'name' => 'Sales Presentations',
                'description' => 'Presentation templates and slides for sales meetings',
                'color' => '#3B82F6',
                'icon' => 'presentation-chart-bar',
                'sort_order' => 1,
            ],
            [
                'name' => 'Product Materials',
                'description' => 'Product brochures, datasheets, and specifications',
                'color' => '#10B981',
                'icon' => 'document-text',
                'sort_order' => 2,
            ],
            [
                'name' => 'Case Studies',
                'description' => 'Customer success stories and case studies',
                'color' => '#F59E0B',
                'icon' => 'star',
                'sort_order' => 3,
            ],
            [
                'name' => 'Battle Cards',
                'description' => 'Competitive analysis and positioning cards',
                'color' => '#EF4444',
                'icon' => 'shield-check',
                'sort_order' => 4,
            ],
            [
                'name' => 'Templates',
                'description' => 'Email templates, proposals, and contracts',
                'color' => '#8B5CF6',
                'icon' => 'template',
                'sort_order' => 5,
            ],
        ];

        foreach ($categories as $categoryData) {
            SalesContentCategory::firstOrCreate(
                ['slug' => \Str::slug($categoryData['name'])],
                $categoryData
            );
        }

        // Create Achievements
        $achievements = [
            [
                'name' => 'First Deal',
                'description' => 'Close your first deal',
                'category' => 'sales',
                'type' => 'boolean',
                'criteria' => ['deals_closed' => 1],
                'points' => 100,
                'rarity' => 'common',
                'icon' => 'currency-dollar',
            ],
            [
                'name' => 'Deal Closer',
                'description' => 'Close 10 deals',
                'category' => 'sales',
                'type' => 'numeric',
                'criteria' => ['deals_closed' => 10],
                'points' => 500,
                'rarity' => 'uncommon',
                'icon' => 'trophy',
            ],
            [
                'name' => 'Sales Rockstar',
                'description' => 'Close 50 deals',
                'category' => 'sales',
                'type' => 'numeric',
                'criteria' => ['deals_closed' => 50],
                'points' => 2000,
                'rarity' => 'rare',
                'icon' => 'star',
            ],
            [
                'name' => 'Revenue Generator',
                'description' => 'Generate $100,000 in revenue',
                'category' => 'sales',
                'type' => 'numeric',
                'criteria' => ['revenue_generated' => 100000],
                'points' => 1000,
                'rarity' => 'uncommon',
                'icon' => 'cash',
            ],
            [
                'name' => 'Communication Master',
                'description' => 'Make 100 calls',
                'category' => 'activity',
                'type' => 'numeric',
                'criteria' => ['calls_made' => 100],
                'points' => 300,
                'rarity' => 'common',
                'icon' => 'phone',
            ],
            [
                'name' => 'Email Champion',
                'description' => 'Send 500 emails',
                'category' => 'activity',
                'type' => 'numeric',
                'criteria' => ['emails_sent' => 500],
                'points' => 250,
                'rarity' => 'common',
                'icon' => 'mail',
            ],
            [
                'name' => 'Playbook Follower',
                'description' => 'Complete 5 sales playbooks',
                'category' => 'learning',
                'type' => 'numeric',
                'criteria' => ['playbooks_completed' => 5],
                'points' => 400,
                'rarity' => 'uncommon',
                'icon' => 'book-open',
            ],
            [
                'name' => 'Consistency King',
                'description' => 'Log activities for 30 consecutive days',
                'category' => 'activity',
                'type' => 'streak',
                'criteria' => ['activity_streak_days' => 30],
                'points' => 800,
                'rarity' => 'rare',
                'icon' => 'calendar',
            ],
        ];

        foreach ($achievements as $achievementData) {
            Achievement::firstOrCreate(
                ['slug' => \Str::slug($achievementData['name'])],
                $achievementData
            );
        }

        // Create Sample Battle Cards
        $battleCards = [
            [
                'title' => 'Salesforce vs Stafe CRM',
                'competitor_name' => 'Salesforce',
                'overview' => 'Salesforce is the market leader in CRM but comes with complexity and high costs that many businesses find overwhelming.',
                'our_strengths' => [
                    'Simple, intuitive interface',
                    'Affordable pricing',
                    'Quick implementation',
                    'Built-in AI features',
                    'No complex customizations needed'
                ],
                'our_weaknesses' => [
                    'Smaller market presence',
                    'Fewer third-party integrations',
                    'Less enterprise features'
                ],
                'competitor_strengths' => [
                    'Market leader',
                    'Extensive customization',
                    'Large ecosystem',
                    'Enterprise features'
                ],
                'competitor_weaknesses' => [
                    'Complex to implement',
                    'Expensive',
                    'Steep learning curve',
                    'Over-engineered for most businesses'
                ],
                'key_differentiators' => [
                    'Out-of-the-box AI features',
                    '90% faster implementation',
                    '70% lower total cost of ownership',
                    'User-friendly design'
                ],
                'objection_handling' => [
                    [
                        'objection' => '"We need enterprise-grade features"',
                        'response' => 'Stafe CRM provides all essential enterprise features while maintaining simplicity. We focus on the 80% of features businesses actually use.'
                    ],
                    [
                        'objection' => '"Salesforce has more integrations"',
                        'response' => 'While Salesforce has many integrations, most businesses only use 5-10. We provide the most important integrations out of the box.'
                    ]
                ],
                'winning_strategies' => [
                    'Demonstrate quick setup vs Salesforce complexity',
                    'Show total cost comparison over 3 years',
                    'Highlight AI features included by default',
                    'Emphasize user adoption rates'
                ],
                'threat_level' => 'high',
                'status' => 'active',
                'win_rate' => 65,
            ],
            [
                'title' => 'HubSpot vs Stafe CRM',
                'competitor_name' => 'HubSpot',
                'overview' => 'HubSpot is known for its marketing automation but has expanded into CRM. Strong in inbound marketing but CRM features can be limited.',
                'our_strengths' => [
                    'Better sales-focused features',
                    'Advanced AI capabilities',
                    'More affordable scaling',
                    'Better deal management',
                    'Superior reporting'
                ],
                'our_weaknesses' => [
                    'Less marketing automation',
                    'Smaller brand recognition',
                    'Fewer marketing templates'
                ],
                'competitor_strengths' => [
                    'Strong marketing platform',
                    'Good content management',
                    'Free tier available',
                    'Strong community'
                ],
                'competitor_weaknesses' => [
                    'Limited sales features',
                    'Expensive for sales teams',
                    'Complex pricing structure',
                    'Not sales-first design'
                ],
                'key_differentiators' => [
                    'Sales-first design philosophy',
                    'Advanced deal management',
                    'Built-in AI for sales',
                    'Better price-to-value ratio'
                ],
                'threat_level' => 'medium',
                'status' => 'active',
                'win_rate' => 78,
            ]
        ];

        foreach ($battleCards as $cardData) {
            BattleCard::firstOrCreate(
                ['slug' => \Str::slug($cardData['title'])],
                array_merge($cardData, ['created_by' => 1, 'updated_by' => 1])
            );
        }

        // Create Sample Sales Playbooks
        $playbooks = [
            [
                'title' => 'Discovery Call Playbook',
                'description' => 'A comprehensive guide for conducting effective discovery calls with prospects',
                'type' => 'discovery',
                'difficulty_level' => 'beginner',
                'overview' => 'This playbook guides you through a structured discovery process to understand prospect needs, pain points, and decision-making criteria.',
                'objectives' => [
                    'Understand prospect\'s current situation',
                    'Identify pain points and challenges',
                    'Discover decision-making process',
                    'Qualify budget and timeline',
                    'Build rapport and trust'
                ],
                'prerequisites' => [
                    'Research the prospect\'s company',
                    'Review their website and recent news',
                    'Prepare relevant questions',
                    'Set up call recording if permitted'
                ],
                'estimated_duration' => '45-60 minutes',
                'status' => 'published',
                'created_by' => 1,
                'updated_by' => 1,
            ],
            [
                'title' => 'Demo Presentation Playbook',
                'description' => 'Master the art of product demonstrations that convert prospects into customers',
                'type' => 'demo',
                'difficulty_level' => 'intermediate',
                'overview' => 'Learn how to deliver compelling product demonstrations that focus on prospect needs and drive decision-making.',
                'objectives' => [
                    'Customize demo to prospect needs',
                    'Highlight relevant features and benefits',
                    'Handle objections during demo',
                    'Create urgency and next steps',
                    'Secure commitment for next meeting'
                ],
                'prerequisites' => [
                    'Complete discovery call',
                    'Understand prospect requirements',
                    'Prepare customized demo environment',
                    'Review objection handling techniques'
                ],
                'estimated_duration' => '60-90 minutes',
                'status' => 'published',
                'created_by' => 1,
                'updated_by' => 1,
            ]
        ];

        foreach ($playbooks as $playbookData) {
            $playbook = SalesPlaybook::firstOrCreate(
                ['slug' => \Str::slug($playbookData['title'])],
                $playbookData
            );

            // Add steps for Discovery Call Playbook
            if ($playbook->title === 'Discovery Call Playbook') {
                $steps = [
                    [
                        'title' => 'Opening and Rapport Building',
                        'description' => 'Start the call with a warm introduction and establish rapport',
                        'instructions' => 'Begin with a friendly greeting, thank them for their time, and establish a connection through small talk or common interests.',
                        'step_type' => 'action',
                        'content' => [
                            'script' => 'Hi [Name], thank you for taking the time to speak with me today. How has your week been so far?',
                            'tips' => ['Be genuine and authentic', 'Listen actively', 'Find common ground']
                        ],
                        'estimated_duration_minutes' => 5,
                        'sort_order' => 1,
                    ],
                    [
                        'title' => 'Set Call Agenda',
                        'description' => 'Establish the structure and goals for the call',
                        'instructions' => 'Outline what you hope to accomplish during the call and confirm the allocated time.',
                        'step_type' => 'script',
                        'content' => [
                            'script' => 'I\'ve scheduled 45 minutes for our call today. I\'d like to learn more about your current situation, understand your challenges, and see if there\'s a way we can help. Does that work for you?'
                        ],
                        'estimated_duration_minutes' => 3,
                        'sort_order' => 2,
                    ],
                    [
                        'title' => 'Current Situation Analysis',
                        'description' => 'Understand the prospect\'s current tools and processes',
                        'instructions' => 'Ask open-ended questions to understand their current situation, tools, and workflows.',
                        'step_type' => 'question',
                        'content' => [
                            'questions' => [
                                'Can you walk me through your current sales process?',
                                'What tools are you currently using for CRM?',
                                'How is your team currently managing leads and opportunities?',
                                'What\'s working well with your current setup?'
                            ]
                        ],
                        'estimated_duration_minutes' => 10,
                        'sort_order' => 3,
                    ],
                    [
                        'title' => 'Pain Point Discovery',
                        'description' => 'Identify specific challenges and pain points',
                        'instructions' => 'Dig deeper into challenges and quantify the impact where possible.',
                        'step_type' => 'question',
                        'content' => [
                            'questions' => [
                                'What are the biggest challenges you\'re facing with your current system?',
                                'How much time does your team spend on administrative tasks?',
                                'Have you lost any deals due to process issues?',
                                'What would happen if you don\'t solve these challenges?'
                            ]
                        ],
                        'estimated_duration_minutes' => 15,
                        'sort_order' => 4,
                    ],
                    [
                        'title' => 'Decision Process and Timeline',
                        'description' => 'Understand how decisions are made and timing',
                        'instructions' => 'Learn about their decision-making process, key stakeholders, and timeline.',
                        'step_type' => 'question',
                        'content' => [
                            'questions' => [
                                'Who else would be involved in this decision?',
                                'What\'s your timeline for making a decision?',
                                'What\'s driving the urgency around this?',
                                'How do you typically evaluate new solutions?'
                            ]
                        ],
                        'estimated_duration_minutes' => 8,
                        'sort_order' => 5,
                    ],
                    [
                        'title' => 'Next Steps and Close',
                        'description' => 'Summarize key points and establish next steps',
                        'instructions' => 'Recap what you learned, confirm mutual interest, and schedule follow-up.',
                        'step_type' => 'action',
                        'content' => [
                            'summary_points' => [
                                'Recap their main challenges',
                                'Confirm your understanding',
                                'Propose next steps',
                                'Schedule follow-up meeting'
                            ]
                        ],
                        'estimated_duration_minutes' => 4,
                        'sort_order' => 6,
                    ],
                ];

                foreach ($steps as $stepData) {
                    PlaybookStep::firstOrCreate(
                        [
                            'playbook_id' => $playbook->id,
                            'title' => $stepData['title'],
                            'sort_order' => $stepData['sort_order']
                        ],
                        array_merge($stepData, ['playbook_id' => $playbook->id])
                    );
                }
            }

            // Add steps for Demo Presentation Playbook
            if ($playbook->title === 'Demo Presentation Playbook') {
                $steps = [
                    [
                        'title' => 'Pre-Demo Setup',
                        'description' => 'Prepare the demo environment and confirm technical setup',
                        'instructions' => 'Ensure your demo environment is ready, test screen sharing, and confirm all participants can see and hear clearly.',
                        'step_type' => 'checklist',
                        'content' => [
                            'checklist' => [
                                'Demo environment prepared with relevant data',
                                'Screen sharing tested and working',
                                'Audio/video quality confirmed',
                                'Backup plan ready if technology fails',
                                'Demo script and talking points reviewed'
                            ]
                        ],
                        'estimated_duration_minutes' => 5,
                        'sort_order' => 1,
                    ],
                    [
                        'title' => 'Demo Introduction',
                        'description' => 'Set context and expectations for the demonstration',
                        'instructions' => 'Briefly recap their needs and explain what you\'ll be showing them.',
                        'step_type' => 'script',
                        'content' => [
                            'script' => 'Based on our last conversation, you mentioned challenges with [specific pain points]. Today I\'m going to show you exactly how Stafe CRM addresses these challenges. I\'ll focus on [specific features] that are most relevant to your situation.'
                        ],
                        'estimated_duration_minutes' => 3,
                        'sort_order' => 2,
                    ],
                    [
                        'title' => 'Core Feature Demonstration',
                        'description' => 'Demonstrate the key features that address their specific needs',
                        'instructions' => 'Focus on features that solve their specific pain points. Use their use cases and terminology.',
                        'step_type' => 'action',
                        'content' => [
                            'demo_flow' => [
                                'Show relevant dashboard view',
                                'Demonstrate key workflows',
                                'Highlight automation features',
                                'Show reporting capabilities',
                                'Connect features to their specific needs'
                            ]
                        ],
                        'estimated_duration_minutes' => 35,
                        'sort_order' => 3,
                    ],
                    [
                        'title' => 'Handle Questions and Objections',
                        'description' => 'Address any questions or concerns that arise',
                        'instructions' => 'Pause regularly for questions. Address objections with specific examples and benefits.',
                        'step_type' => 'decision',
                        'content' => [
                            'common_objections' => [
                                'Price concerns -> Focus on ROI and value',
                                'Feature gaps -> Explain roadmap or workarounds',
                                'Integration concerns -> Show specific examples',
                                'Security questions -> Provide detailed information'
                            ]
                        ],
                        'estimated_duration_minutes' => 10,
                        'sort_order' => 4,
                    ],
                    [
                        'title' => 'Create Urgency and Close',
                        'description' => 'Summarize value and move toward next steps',
                        'instructions' => 'Recap how the solution addresses their needs and propose specific next steps.',
                        'step_type' => 'action',
                        'content' => [
                            'closing_approach' => [
                                'Summarize key benefits shown',
                                'Ask for their thoughts and reactions',
                                'Address any remaining concerns',
                                'Propose trial or next meeting',
                                'Create urgency with time-sensitive offers'
                            ]
                        ],
                        'estimated_duration_minutes' => 7,
                        'sort_order' => 5,
                    ],
                ];

                foreach ($steps as $stepData) {
                    PlaybookStep::firstOrCreate(
                        [
                            'playbook_id' => $playbook->id,
                            'title' => $stepData['title'],
                            'sort_order' => $stepData['sort_order']
                        ],
                        array_merge($stepData, ['playbook_id' => $playbook->id])
                    );
                }
            }
        }
    }
}