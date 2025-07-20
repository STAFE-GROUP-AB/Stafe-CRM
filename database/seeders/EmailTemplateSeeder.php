<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\EmailTemplate;

class EmailTemplateSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $templates = [
            [
                'name' => 'Welcome Email',
                'slug' => 'welcome-email',
                'subject' => 'Welcome to {{company_name}}!',
                'body' => "Hi {{contact_name}},\n\nWelcome to {{company_name}}! We're excited to work with you.\n\nBest regards,\n{{sender_name}}",
                'type' => 'contact',
                'variables' => ['contact_name', 'company_name', 'sender_name'],
            ],
            [
                'name' => 'Deal Follow Up',
                'slug' => 'deal-follow-up',
                'subject' => 'Following up on {{deal_name}}',
                'body' => "Hi {{contact_name}},\n\nI wanted to follow up on our discussion about {{deal_name}}.\n\nDeal Value: {{deal_value}}\nExpected Close Date: {{expected_close_date}}\n\nPlease let me know if you have any questions.\n\nBest regards,\n{{sender_name}}",
                'type' => 'deal',
                'variables' => ['contact_name', 'deal_name', 'deal_value', 'expected_close_date', 'sender_name'],
            ],
            [
                'name' => 'Task Reminder',
                'slug' => 'task-reminder',
                'subject' => 'Reminder: {{task_title}}',
                'body' => "Hi {{contact_name}},\n\nThis is a reminder about: {{task_title}}\n\nDue Date: {{due_date}}\nPriority: {{priority}}\n\nDescription:\n{{task_description}}\n\nBest regards,\n{{sender_name}}",
                'type' => 'task',
                'variables' => ['contact_name', 'task_title', 'due_date', 'priority', 'task_description', 'sender_name'],
            ],
            [
                'name' => 'Meeting Invitation',
                'slug' => 'meeting-invitation',
                'subject' => 'Meeting Invitation: {{meeting_title}}',
                'body' => "Hi {{contact_name}},\n\nYou're invited to a meeting:\n\nTitle: {{meeting_title}}\nDate: {{meeting_date}}\nTime: {{meeting_time}}\nLocation: {{meeting_location}}\n\nPlease confirm your attendance.\n\nBest regards,\n{{sender_name}}",
                'type' => 'task',
                'variables' => ['contact_name', 'meeting_title', 'meeting_date', 'meeting_time', 'meeting_location', 'sender_name'],
            ],
            [
                'name' => 'Thank You',
                'slug' => 'thank-you',
                'subject' => 'Thank you for your business!',
                'body' => "Hi {{contact_name}},\n\nThank you for choosing {{company_name}}! We appreciate your business and look forward to serving you.\n\nIf you have any questions or need assistance, please don't hesitate to reach out.\n\nBest regards,\n{{sender_name}}",
                'type' => 'general',
                'variables' => ['contact_name', 'company_name', 'sender_name'],
            ],
        ];

        foreach ($templates as $template) {
            if (!EmailTemplate::where('slug', $template['slug'])->exists()) {
                EmailTemplate::create($template);
            }
        }
    }
}