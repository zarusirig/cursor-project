/**
 * CNP Automation Dashboard JavaScript
 */

(function($) {
  'use strict';

  var CNPDashboard = {
    init: function() {
      this.bindEvents();
      this.loadDashboard();
    },

    bindEvents: function() {
      var self = this;

      // Handle filter changes
      $('.cnp-dashboard-filter-form select').on('change', function() {
        self.loadDashboard();
      });

      // Handle export clicks
      $(document).on('click', '.cnp-tile-export', function(e) {
        e.preventDefault();
        var metric = $(this).data('metric');
        self.exportMetric(metric);
      });
    },

    loadDashboard: function() {
      var self = this;
      var $grid = $('#cnp-dashboard-grid');
      var $filters = $('.cnp-dashboard-filter-form');

      // Show loading state
      $grid.html('<div class="cnp-dashboard-loading"><p>Loading dashboard data...</p></div>');

      // Get filter values
      var range = $filters.find('select[name="range"]').val() || 7;
      var category = $filters.find('select[name="category"]').val() || 'all';

      // Make AJAX request
      $.ajax({
        url: cnpDashboard.ajaxUrl,
        type: 'POST',
        data: {
          action: 'cnp_dashboard_data',
          range: range,
          category: category,
          nonce: cnpDashboard.nonce
        },
        success: function(response) {
          if (response.success) {
            self.renderDashboard(response.data);
          } else {
            self.showError('Failed to load dashboard data');
          }
        },
        error: function() {
          self.showError('Network error while loading dashboard');
        }
      });
    },

    renderDashboard: function(data) {
      var template = $('#cnp-dashboard-template').html();
      var html = _.template(template)(data);
      $('#cnp-dashboard-grid').html(html);
    },

    exportMetric: function(metric) {
      var $filters = $('.cnp-dashboard-filter-form');
      var range = $filters.find('select[name="range"]').val() || 7;

      var url = ajaxurl + '?action=cnp_export_metric&metric=' + encodeURIComponent(metric) +
                '&range=' + range + '&_wpnonce=' + cnpDashboard.nonce;

      // Create temporary link and click it
      var $link = $('<a>')
        .attr('href', url)
        .attr('download', metric + '-metrics.csv')
        .css('display', 'none');

      $('body').append($link);
      $link[0].click();
      $link.remove();
    },

    showError: function(message) {
      $('#cnp-dashboard-grid').html(
        '<div class="cnp-dashboard-loading"><p style="color: #d63638;">' + message + '</p></div>'
      );
    }
  };

  // Initialize when document is ready
  $(document).ready(function() {
    CNPDashboard.init();
  });

})(jQuery);
