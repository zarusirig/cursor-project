# n8n Flow: News Intake

This guide outlines the step‑by‑step workflow in n8n to ingest news from multiple sources, enrich it, and draft posts in WordPress. It supports our requirement to add a value‑add layer to every piece of content【490377143752729†L62-L74】.

## Workflow Steps
1. **Cron Trigger:** Schedule the flow to run at an interval appropriate for each source (e.g., every hour for government feeds; every 15 minutes for tech news).

2. **RSS/HTTP Request Nodes:** For each source, pull the latest entries. Use the HTTP Request node if the source requires an API key or custom headers.

3. **Normalization:** Use a Function node to map source fields (title, URL, publish date, content) into a standard format. Create a unique hash (e.g., SHA‑256 of the title + source) for deduplication.

4. **Deduplication:** Query a database (SQLite/MySQL) for existing hashes. If the item exists, exit the workflow; otherwise, insert the hash and continue.

5. **Content Retrieval:** For sources providing only summaries, fetch the full article via an HTTP Request or use a readability parser. Store the full text for analysis.

6. **AI Enrichment:** Use a Code node or external API to:
   - Generate a one‑paragraph summary.
   - Extract entities (people, organisations, places) and match them to our tag allowlist.
   - Produce bullet points explaining what’s new and why it matters.

7. **Policy Gate:** Apply a conditional node to block items if they lack value‑add potential (e.g., duplicates, trivial announcements)【490377143752729†L62-L74】.

8. **WordPress Draft Creation:** Use the WordPress node to create a Draft post with:
   - Title (cleaned; avoid clickbait).
   - Excerpt or summary.
   - Full content as the post body, including the enrichment output.
   - Category and tags determined by the extracted entities and the IA blueprint【490377143752729†L205-L213】.
   - Custom fields for original source URL and value‑add notes.

9. **Notification:** Send a Slack or email message to editors with a link to the draft and a summary of the enrichment outputs.

10. **Human Review:** Editors evaluate the draft, add their own analysis, verify facts, and decide whether to publish, revise, or discard.

## Additional Considerations
- **Rate Limiting:** Use Wait nodes or limit concurrency when dealing with APIs.
- **Error Handling:** Configure Error Trigger nodes to catch failures. Notify the technical team if repeated errors occur.
- **Compliance:** Ensure all automation complies with our sponsored content policy. Do not automatically publish; always set posts to Draft.

---

**Owner:** WordPress Developer & Data Analyst  
**Success Metrics:** Automated drafts contain summaries and entity tags; human review required before publishing; zero duplicate stories reaching the site.
