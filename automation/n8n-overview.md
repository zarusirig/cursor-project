# n8n Automation Overview

n8n is the backbone of our newsroom automation. It orchestrates content ingestion, enrichment, deduplication, and publishing. This overview describes the architecture and best practices.

## Architecture
n8n operates as a self‑hosted workflow engine. Workflows consist of nodes connected in a sequence to perform tasks. Key components:

- **Trigger Nodes:** Cron triggers check sources on a schedule (e.g., every 15 minutes) to fetch new content.
- **Data Sources:** RSS feeds, REST APIs, or webhooks that supply raw news items.
- **Processing Nodes:** Code nodes normalise data, compute hashes for deduplication, and call APIs for enrichment (e.g., NLP for entity extraction).
- **Storage:** SQLite/MySQL databases store hashes to prevent duplicates and track processed items.
- **Action Nodes:** WordPress node creates Draft posts with categories/tags assigned and attaches enrichment outputs.
- **Notification Nodes:** Slack or email nodes alert editors when drafts are ready for review.

## Best Practices
1. **Modular Workflows:** Break complex processes into smaller workflows that can be reused (e.g., a common deduplication module).
2. **Rate Limits & Error Handling:** Respect API rate limits. Use error triggers and retries to handle transient failures.
3. **Secrets Management:** Store API keys and credentials in n8n’s credential manager, not in plain text within nodes.
4. **Logging & Monitoring:** Enable execution logs and set up alerts for failures. Monitor throughput and latency to identify bottlenecks.
5. **Separation of Concerns:** Use environment variables to define staging vs production endpoints.
6. **Security:** Run n8n behind HTTPS and secure it with basic auth or JWT. Restrict network access to approved APIs.

## Editor Involvement
Although n8n automates the ingestion and drafting process, human editors remain in the loop. Workflows must pause at key points to allow editorial review and value‑add. The final “Publish” action is always a human decision.

---

**Owner:** WordPress Developer & Data Analyst  
**Success Metrics:** Automated ingestion runs reliably; duplicate rate remains below 1 %; average time from source discovery to draft creation is under 30 minutes.
