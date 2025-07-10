# Unified Communications Platform - Usage Guide

## Overview

The Unified Communications Platform provides comprehensive communication capabilities including:

- **Voice & Video Calling** via Twilio integration
- **SMS & WhatsApp Messaging** with automated routing
- **AI-Powered Call Transcription** with sentiment analysis
- **Live Chat & Chatbots** with lead qualification
- **Social Media Intelligence** (framework ready)

## Quick Setup

### 1. Environment Configuration

Add these variables to your `.env` file:

```env
# Twilio Configuration
TWILIO_ACCOUNT_SID=your_account_sid
TWILIO_AUTH_TOKEN=your_auth_token
TWILIO_PHONE_NUMBER=+1234567890
TWILIO_WHATSAPP_NUMBER=+1234567890
TWILIO_WEBHOOK_SECRET=your_webhook_secret

# OpenAI Configuration (for transcription and chat)
OPENAI_API_KEY=your_openai_api_key
OPENAI_ORGANIZATION=your_org_id
```

### 2. Database Migration

Run the database migrations to create the communication tables:

```bash
php artisan migrate
```

### 3. Configure AI Providers

1. Go to `/ai/configuration` in your CRM
2. Add your AI provider credentials (OpenAI, Anthropic, or Google)
3. Set up default models for transcription and chat

## Features

### Unified Communication Hub

Access the communication hub at `/communications` to:

- View all communications in one place
- Filter by type, direction, status, and date
- Make outbound calls and send SMS messages
- Monitor call recordings and transcripts
- Review AI-generated insights and follow-up suggestions

### Live Chat Widget

#### Embedding the Chat Widget

Add this script to any website to enable live chat:

```html
<script src="https://your-crm-domain.com/chat/embed.js"></script>
```

#### JavaScript API

The chat widget provides a JavaScript API for custom integration:

```javascript
// Open chat widget
StafeChat.open();

// Close chat widget
StafeChat.close();

// Toggle chat widget
StafeChat.toggle();
```

#### Customization

The chat widget can be customized through the configuration in `ChatWidgetController`:

- Colors and branding
- Welcome messages
- Business hours
- Auto-open behavior

### Voice & Video Calling

#### Making Calls

1. Navigate to Communications Hub
2. Click "Make Call"
3. Select a contact or enter a phone number
4. Call will be initiated via Twilio

#### Call Features

- **Automatic Recording**: All calls are recorded by default
- **Real-time Transcription**: Powered by OpenAI Whisper
- **Sentiment Analysis**: AI analyzes conversation tone
- **Follow-up Suggestions**: Automated next steps recommendations

### SMS & WhatsApp Messaging

#### Sending Messages

1. Go to Communications Hub
2. Click "Send SMS"
3. Choose contact and compose message
4. Message sent via Twilio

#### Incoming Messages

- Automatic webhook handling for incoming SMS/WhatsApp
- AI sentiment analysis and intent detection
- Auto-response capabilities during business hours

### AI-Powered Features

#### Call Transcription

- Real-time transcription using OpenAI Whisper
- Speaker identification
- Keyword highlighting
- Searchable transcript archive

#### Chatbot Intelligence

- Context-aware responses using existing Prism AI
- Lead qualification and scoring
- Automatic escalation to human agents
- Conversation history preservation

#### Sentiment Analysis

- Real-time sentiment scoring (-1 to 1)
- Emotional intelligence insights
- Conversation quality metrics

## Webhooks Setup

### Twilio Webhooks

Configure these webhook URLs in your Twilio console:

```
Voice:
- Incoming: https://your-domain.com/webhooks/twilio/voice/incoming
- Status: https://your-domain.com/webhooks/twilio/voice/status
- TwiML: https://your-domain.com/webhooks/twilio/voice/twiml

SMS:
- Incoming: https://your-domain.com/webhooks/twilio/sms/incoming
- Status: https://your-domain.com/webhooks/twilio/sms/status

WhatsApp:
- Incoming: https://your-domain.com/webhooks/twilio/whatsapp/incoming
- Status: https://your-domain.com/webhooks/twilio/whatsapp/status

Recording:
- Complete: https://your-domain.com/webhooks/twilio/recording/status
```

## Lead Qualification

The chat system automatically qualifies leads based on:

- **Message Content**: High-intent keywords (pricing, demo, etc.)
- **Engagement Level**: Response time and message frequency
- **Sentiment Score**: Positive engagement indicators
- **Contact Information**: Email and phone number collection

Qualified leads are automatically:
- Scored and flagged in the system
- Added to the contacts database
- Assigned follow-up tasks

## Agent Workflows

### Chat Agent Interface

Agents can:
- Monitor active chat sessions
- Take over from AI chatbots
- View visitor information and chat history
- Access lead qualification scores
- End completed sessions

### Communication Management

- Unified inbox for all communication types
- Real-time notifications for new messages
- AI-powered insights and suggestions
- Quick actions for common responses

## API Integration

All communication features are built on the existing CRM API structure and can be integrated with external systems.

### Key Models

- `Communication`: Core communication record
- `ChatSession`: Live chat sessions
- `ChatMessage`: Individual chat messages
- `SocialMediaAccount`: Social platform connections

### Services

- `TwilioService`: Voice, SMS, WhatsApp integration
- `TranscriptionService`: AI-powered transcription
- `ChatBotService`: AI chat responses and qualification
- `SocialMediaService`: Social monitoring framework

## Customization

### AI Models

The system uses the existing Prism AI infrastructure, allowing you to:
- Configure different AI providers per user
- Customize response templates
- Adjust sentiment analysis thresholds
- Train custom models for specific use cases

### Business Rules

Customize chat behavior through:
- Business hours configuration
- Auto-escalation triggers
- Lead scoring criteria
- Response templates

## Troubleshooting

### Common Issues

1. **Twilio Not Configured**: Ensure all environment variables are set
2. **AI Not Responding**: Check AI provider configuration in `/ai/configuration`
3. **Webhooks Failing**: Verify webhook URLs in Twilio console
4. **Chat Widget Not Loading**: Check domain configuration and CORS settings

### Logs

All communication activities are logged for debugging:
- Laravel logs for service errors
- Communication records for all interactions
- AI analysis results and confidence scores

## Future Enhancements

The platform is designed to be extensible. Planned features include:

- **Social Media Integration**: Full LinkedIn, Twitter, Facebook monitoring
- **Video Conferencing**: WebRTC-based video calls
- **Advanced Analytics**: Communication performance dashboards
- **Mobile App**: Native iOS/Android applications
- **CRM Integrations**: Salesforce, HubSpot, and other CRM connectors

## Support

For technical support or feature requests, refer to the main CRM documentation or contact the development team.