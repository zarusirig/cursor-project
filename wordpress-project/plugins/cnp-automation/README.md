# CNP Automation Plugin

A comprehensive content generation pipeline for WordPress that accepts briefs via REST/CLI, enqueues jobs, generates AI-assisted drafts, and forces pre-publish QA checklists.

## Features

- **Queue Management**: Custom database table with status tracking and atomic locking
- **REST API**: Full CRUD operations for job management with authentication
- **CLI Tools**: WP-CLI commands for all operations
- **AI Content Generation**: Stub implementation ready for real AI service integration
- **Editorial Review**: QA checklist system with admin UI
- **Rate Limiting**: Configurable hourly limits
- **Webhooks**: Status change notifications
- **GA4 Analytics**: Event tracking for generated content

## Installation

1. Copy the `cnp-automation` folder to `wp-content/plugins/`
2. Activate the plugin through WordPress admin
3. Configure settings under **CNP → Automation**

## Configuration

### API & Security
- **Enable API**: Toggle REST API access
- **API Keys**: Newline-separated list of Bearer token keys

### Defaults for Generated Posts
- **Default Status**: Draft or Pending Review
- **Default Author**: User ID or current user
- **Default Category**: Fallback category ID
- **Default Tags**: Comma-separated tag list
- **Default Language**: ISO language code

### AI Disclosure & Policy
- **Auto-insert AI Disclosure**: Add disclosure block to content
- **AI Disclosure Block**: HTML content for disclosure
- **Require Sources**: Enforce sources for factual claims
- **Max Generate per Hour**: Rate limiting threshold

### Webhooks & Notifications
- **Job Status Webhook URL**: POST endpoint for status changes
- **Notification Email**: Address for error notifications

## API Usage

### Authentication

Use Bearer token authentication:
```
Authorization: Bearer your-api-key
```

Or server-to-server with query parameter:
```
?cnp_key=your-api-key
```

### Endpoints

#### Create Job
```http
POST /wp-json/cnp-automation/v1/jobs
Content-Type: application/json

{
  "title": "Article Title",
  "category_slug": "technology",
  "tags": ["tag1", "tag2"],
  "sources": [{"url": "...", "label": "..."}]
}
```

#### Get Job
```http
GET /wp-json/cnp-automation/v1/jobs/{id}
```

#### Cancel Job
```http
POST /wp-json/cnp-automation/v1/jobs/{id}/cancel
```

#### List Jobs (Admin)
```http
GET /wp-json/cnp-automation/v1/jobs?status=queued&limit=50
```

## CLI Usage

### Enqueue Jobs
```bash
wp cnp:jobs:enqueue --file=/path/to/briefs.json
```

### Process Queue
```bash
wp cnp:jobs:run --max=3
```

### List Jobs
```bash
wp cnp:jobs:list --status=queued --limit=50
wp cnp:jobs:list  # All jobs
```

### Job Management
```bash
wp cnp:jobs:retry --id=123
wp cnp:jobs:cancel --id=123
wp cnp:jobs:stats
```

## Brief Format

```json
{
  "title": "Required article title (8-120 chars)",
  "dek": "Optional short description",
  "category_slug": "Optional category slug",
  "category_id": "Optional category ID",
  "tags": ["Optional", "tag", "array"],
  "language": "Optional ISO language code",
  "author_id": "Optional author user ID",
  "target_template": "Optional: article|review|comparison|explainer|live",
  "entities": ["Optional", "topic", "array"],
  "internal_links_desired": "Optional link count",
  "featured_image_url": "Optional absolute image URL",
  "sources": [{"url": "...", "label": "..."}],
  "notes": "Optional editor notes",
  "priority": "Optional 0-10 (higher = processed first)",
  "external_id": "Optional correlation ID"
}
```

## Testing

1. **Setup**: Configure API keys and settings
2. **Enqueue**: Use CLI or REST to create jobs
3. **Process**: Run queue processor manually or via cron
4. **Review**: Edit generated posts, complete QA checklist
5. **Publish**: Posts ready for publication after review

### Sample Test Commands

```bash
# Enqueue test briefs
wp cnp:jobs:enqueue --file=test-briefs.json

# Check queue status
wp cnp:jobs:stats

# Process jobs
wp cnp:jobs:run --max=2

# List completed jobs
wp cnp:jobs:list --status=draft_created
```

### REST API Testing

```bash
# Create a job
curl -X POST http://localhost/wp-json/cnp-automation/v1/jobs \
  -H "Authorization: Bearer your-key" \
  -H "Content-Type: application/json" \
  -d '{"title":"Test Article","category_slug":"uncategorized"}'

# Check job status
curl http://localhost/wp-json/cnp-automation/v1/jobs/1 \
  -H "Authorization: Bearer your-key"
```

## Architecture

### Database Schema

```sql
wp_cnp_jobs (
  id INT AUTO_INCREMENT PRIMARY KEY,
  created_at DATETIME,
  updated_at DATETIME,
  status ENUM('queued','generating','draft_created','needs_review','published','failed','cancelled'),
  brief LONGTEXT, -- JSON
  post_id INT NULL,
  error TEXT NULL,
  locks VARCHAR(255) NULL,
  started_at DATETIME NULL,
  finished_at DATETIME NULL,
  priority INT DEFAULT 0
)
```

### Status Flow

```
queued → generating → draft_created → needs_review → published
    ↓         ↓            ↓            ↓
cancelled   failed       failed       failed
```

### Cron Jobs

- **cnp_automation_process_queue**: Runs every minute, processes up to 3 jobs

### Webhooks

POST requests sent on status changes:
```json
{
  "id": 123,
  "status": "needs_review",
  "post_id": 456,
  "external_id": "custom-id",
  "updated_at": "2025-11-12T12:34:56Z"
}
```

## Development

### Adding Real AI Integration

Replace the `generate_content_stub()` function in `inc/jobs.php`:

```php
function generate_content_stub($brief) {
  // Call your AI service here
  $ai_response = call_ai_service($brief);

  return [
    'body' => $ai_response['content'],
    'excerpt' => $ai_response['summary'],
    'prompt_used' => $brief,
  ];
}
```

### Extending QA Checks

Add custom checklist items in `inc/utils.php`:

```php
function generate_qa_checklist() {
  return [
    'hero_present' => false,
    'custom_check' => false, // Your custom check
  ];
}
```

## Security

- API keys validated on every request
- Rate limiting prevents abuse
- Nonce verification on admin forms
- User capability checks
- Input sanitization and validation

## Monitoring

- Plugin logs to `wp-content/cnp-automation.log`
- GA4 events for analytics
- Email notifications for failures
- Webhook notifications for integrations
