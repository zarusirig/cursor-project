/**
 * CNP Auto Featured Images - Admin JavaScript
 */

(function($) {
    'use strict';

    const CNP_AFI = {
        /**
         * Initialize
         */
        init: function() {
            this.bindEvents();
        },

        /**
         * Bind events
         */
        bindEvents: function() {
            // Settings page
            $('#validate-api-key').on('click', this.validateApiKey);
            $('#cnp-afi-settings-form').on('submit', this.saveSettings);

            // Bulk generate page
            $('#load-posts').on('click', this.loadPosts);
            $('#select-all-posts').on('click', this.selectAllPosts);
            $('#deselect-all-posts').on('click', this.deselectAllPosts);
            $('#bulk-generate-btn').on('click', this.startBulkGenerate);
            $(document).on('change', '.post-checkbox', this.updateBulkButton);
        },

        /**
         * Validate API key
         */
        validateApiKey: function(e) {
            e.preventDefault();

            const $button = $(this);
            const $status = $('#api-key-status');
            const apiKey = $('#api_key').val();

            if (!apiKey) {
                alert(cnpAfiData.strings.invalid);
                return;
            }

            $button.prop('disabled', true).text(cnpAfiData.strings.validating);
            $status.removeClass('error').addClass('validating').html('<span class="dashicons dashicons-update"></span> Validating...');

            $.ajax({
                url: cnpAfiData.ajaxUrl,
                type: 'POST',
                data: {
                    action: 'cnp_afi_validate_api_key',
                    nonce: cnpAfiData.nonce,
                    api_key: apiKey
                },
                success: function(response) {
                    if (response.success) {
                        $status.removeClass('validating').html('<span class="dashicons dashicons-yes-alt"></span> ' + cnpAfiData.strings.valid);
                        $('#api_key_valid').val('1');
                        CNP_AFI.showNotice('success', response.data.message);
                    } else {
                        $status.removeClass('validating').addClass('error').html('<span class="dashicons dashicons-dismiss"></span> ' + cnpAfiData.strings.invalid);
                        $('#api_key_valid').val('0');
                        CNP_AFI.showNotice('error', response.data.message || cnpAfiData.strings.invalid);
                    }
                },
                error: function() {
                    $status.removeClass('validating').addClass('error').html('<span class="dashicons dashicons-dismiss"></span> Error');
                    $('#api_key_valid').val('0');
                    CNP_AFI.showNotice('error', 'Connection error. Please try again.');
                },
                complete: function() {
                    $button.prop('disabled', false).text('Validate');
                }
            });
        },

        /**
         * Save settings
         */
        saveSettings: function(e) {
            e.preventDefault();

            const $form = $(this);
            const $button = $form.find('button[type="submit"]');
            const formData = $form.serializeArray();
            formData.push({ name: 'action', value: 'cnp_afi_save_settings' });
            formData.push({ name: 'nonce', value: cnpAfiData.nonce });

            $button.prop('disabled', true).text('Saving...');

            $.ajax({
                url: cnpAfiData.ajaxUrl,
                type: 'POST',
                data: formData,
                success: function(response) {
                    if (response.success) {
                        CNP_AFI.showNotice('success', response.data.message);
                    } else {
                        CNP_AFI.showNotice('error', response.data.message || 'Failed to save settings.');
                    }
                },
                error: function() {
                    CNP_AFI.showNotice('error', 'Connection error. Please try again.');
                },
                complete: function() {
                    $button.prop('disabled', false).text('Save Settings');
                }
            });
        },

        /**
         * Load posts without featured images
         */
        loadPosts: function(e) {
            e.preventDefault();

            const postType = $('#post-type-filter').val();
            const $button = $(this);
            const $loading = $('#posts-loading');
            const $list = $('#posts-list');

            $button.prop('disabled', true);
            $loading.show();
            $list.hide();

            $.ajax({
                url: cnpAfiData.ajaxUrl,
                type: 'POST',
                data: {
                    action: 'cnp_afi_get_posts_without_images',
                    nonce: cnpAfiData.nonce,
                    post_type: postType,
                    limit: 100
                },
                success: function(response) {
                    if (response.success && response.data.posts.length > 0) {
                        CNP_AFI.renderPostsTable(response.data.posts);
                        $('#posts-count').text(`Found ${response.data.count} posts without featured images.`);
                        $list.show();
                        $('#select-all-posts, #deselect-all-posts').show();
                    } else {
                        CNP_AFI.showNotice('info', 'No posts found without featured images.');
                    }
                },
                error: function() {
                    CNP_AFI.showNotice('error', 'Failed to load posts. Please try again.');
                },
                complete: function() {
                    $loading.hide();
                    $button.prop('disabled', false);
                }
            });
        },

        /**
         * Render posts table
         */
        renderPostsTable: function(posts) {
            let html = '<table class="cnp-afi-posts-table">';
            html += '<thead><tr>';
            html += '<th><input type="checkbox" id="select-all-checkbox"></th>';
            html += '<th>Post Title</th>';
            html += '<th>Date</th>';
            html += '<th>Action</th>';
            html += '</tr></thead><tbody>';

            posts.forEach(function(post) {
                html += `<tr data-post-id="${post.ID}">`;
                html += `<td><input type="checkbox" class="post-checkbox" value="${post.ID}"></td>`;
                html += `<td><a href="${post.edit_link}" target="_blank">${post.title}</a></td>`;
                html += `<td>${post.date}</td>`;
                html += `<td><a href="${post.edit_link}" target="_blank" class="button button-small">Edit</a></td>`;
                html += '</tr>';
            });

            html += '</tbody></table>';
            $('#posts-table-container').html(html);

            // Bind select all checkbox
            $('#select-all-checkbox').on('change', function() {
                $('.post-checkbox').prop('checked', $(this).prop('checked'));
                CNP_AFI.updateBulkButton();
            });
        },

        /**
         * Select all posts
         */
        selectAllPosts: function(e) {
            e.preventDefault();
            $('.post-checkbox').prop('checked', true);
            $('#select-all-checkbox').prop('checked', true);
            CNP_AFI.updateBulkButton();
        },

        /**
         * Deselect all posts
         */
        deselectAllPosts: function(e) {
            e.preventDefault();
            $('.post-checkbox').prop('checked', false);
            $('#select-all-checkbox').prop('checked', false);
            CNP_AFI.updateBulkButton();
        },

        /**
         * Update bulk generate button
         */
        updateBulkButton: function() {
            const selectedCount = $('.post-checkbox:checked').length;
            const $button = $('#bulk-generate-btn');

            if (selectedCount > 0) {
                $button.prop('disabled', false).text(`Generate Featured Images for ${selectedCount} Posts`);
            } else {
                $button.prop('disabled', true).text('Generate Featured Images for Selected Posts');
            }
        },

        /**
         * Start bulk generation
         */
        startBulkGenerate: function(e) {
            e.preventDefault();

            const selectedPosts = [];
            $('.post-checkbox:checked').each(function() {
                selectedPosts.push($(this).val());
            });

            if (selectedPosts.length === 0) {
                alert('Please select at least one post.');
                return;
            }

            const confirmMessage = cnpAfiData.strings.confirmBulk.replace('{count}', selectedPosts.length);
            if (!confirm(confirmMessage)) {
                return;
            }

            // Hide posts list, show progress
            $('#posts-list').hide();
            $('#bulk-progress').show();
            $('#progress-fill').css('width', '0%');
            $('#progress-text').text('Starting...');
            $('#progress-log').html('');

            CNP_AFI.processBulkGeneration(selectedPosts, 0);
        },

        /**
         * Process bulk generation in batches
         */
        processBulkGeneration: function(postIds, offset) {
            $.ajax({
                url: cnpAfiData.ajaxUrl,
                type: 'POST',
                data: {
                    action: 'cnp_afi_bulk_generate',
                    nonce: cnpAfiData.nonce,
                    post_ids: postIds,
                    offset: offset
                },
                success: function(response) {
                    if (response.success) {
                        const data = response.data;
                        const progress = Math.round((data.processed / data.total) * 100);

                        // Update progress bar
                        $('#progress-fill').css('width', progress + '%').text(progress + '%');
                        $('#progress-text').text(`Processed ${data.processed} of ${data.total} posts...`);

                        // Add log entries
                        if (data.results.success > 0) {
                            CNP_AFI.addLogEntry('success', `✓ Successfully generated ${data.results.success} images in this batch`);
                        }
                        if (data.results.failed > 0) {
                            CNP_AFI.addLogEntry('error', `✗ Failed to generate ${data.results.failed} images in this batch`);
                            // Log individual errors
                            Object.entries(data.results.errors).forEach(([postId, error]) => {
                                CNP_AFI.addLogEntry('error', `  Post #${postId}: ${error}`);
                            });
                        }

                        // Continue if there are more
                        if (data.has_more) {
                            setTimeout(function() {
                                CNP_AFI.processBulkGeneration(postIds, data.processed);
                            }, 1000); // 1 second delay between batches
                        } else {
                            // Complete
                            CNP_AFI.completeBulkGeneration(data.total, data.results);
                        }
                    } else {
                        CNP_AFI.addLogEntry('error', '✗ Error: ' + (response.data.message || 'Unknown error'));
                        CNP_AFI.showNotice('error', 'Bulk generation failed. Check the log for details.');
                    }
                },
                error: function() {
                    CNP_AFI.addLogEntry('error', '✗ Connection error. Please try again.');
                    CNP_AFI.showNotice('error', 'Connection error during bulk generation.');
                }
            });
        },

        /**
         * Complete bulk generation
         */
        completeBulkGeneration: function(total, finalResults) {
            $('#progress-fill').css('width', '100%').text('100%');

            // Calculate totals across all batches
            const message = cnpAfiData.strings.bulkComplete
                .replace('{success}', finalResults.success || 0)
                .replace('{failed}', finalResults.failed || 0);

            $('#progress-text').html(`<strong>${message}</strong>`);
            CNP_AFI.addLogEntry('info', '═══════════════════════════════');
            CNP_AFI.addLogEntry('success', '✓ Bulk generation complete!');
            CNP_AFI.addLogEntry('info', `Total: ${total} posts processed`);
            CNP_AFI.addLogEntry('info', '═══════════════════════════════');

            CNP_AFI.showNotice('success', message);

            // Add reload button
            setTimeout(function() {
                const $reloadBtn = $('<button class="button button-primary">Reload Page</button>');
                $reloadBtn.on('click', function() {
                    location.reload();
                });
                $('#bulk-progress').append($reloadBtn);
            }, 1000);
        },

        /**
         * Add log entry
         */
        addLogEntry: function(type, message) {
            const $log = $('#progress-log');
            const timestamp = new Date().toLocaleTimeString();
            const entry = `<div class="log-entry ${type}">[${timestamp}] ${message}</div>`;
            $log.append(entry);
            // Auto-scroll to bottom
            $log.scrollTop($log[0].scrollHeight);
        },

        /**
         * Show notice
         */
        showNotice: function(type, message) {
            const $notice = $('<div class="cnp-afi-notice ' + type + '">' + message + '</div>');
            $('.cnp-afi-wrap h1').after($notice);

            // Auto-dismiss after 5 seconds
            setTimeout(function() {
                $notice.fadeOut(function() {
                    $(this).remove();
                });
            }, 5000);
        }
    };

    // Initialize on document ready
    $(document).ready(function() {
        CNP_AFI.init();
    });

})(jQuery);
