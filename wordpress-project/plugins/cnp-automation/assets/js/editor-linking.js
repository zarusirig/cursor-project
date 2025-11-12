// CNP Automation Linking - Block Editor Integration
(function(wp) {
  const { registerPlugin } = wp.plugins;
  const { PluginSidebar, PluginSidebarMoreMenuItem } = wp.editPost;
  const { PanelBody, Button } = wp.components;
  const { useState, useEffect } = wp.element;
  const { useSelect, useDispatch } = wp.data;
  const { apiFetch } = wp;

  // Register sidebar panel
  registerPlugin("cnp-linking-sidebar", {
    render: function() {
      const [suggestions, setSuggestions] = useState([]);
      const [loading, setLoading] = useState(false);

      const postId = useSelect(function(select) {
        return select("core/editor").getCurrentPostId();
      });

      const getSuggestions = function() {
        setLoading(true);

        apiFetch({
          path: "cnp-automation/v1/link-suggestions",
          method: "POST",
          data: { post_id: postId }
        }).then(function(response) {
          setSuggestions(response.suggestions || []);
          setLoading(false);
        }).catch(function(error) {
          console.error("Failed to get suggestions:", error);
          setLoading(false);
        });
      };

      return wp.element.createElement(
        PluginSidebar,
        {
          name: "cnp-linking-sidebar",
          title: "Internal Links",
          icon: "admin-links"
        },
        wp.element.createElement(
          PanelBody,
          { title: "Link Suggestions", initialOpen: true },
          wp.element.createElement(
            Button,
            {
              isPrimary: true,
              onClick: getSuggestions,
              disabled: loading,
              style: { width: "100%", marginBottom: "12px" }
            },
            loading ? "Getting Suggestions..." : "Get Suggestions"
          ),
          suggestions.length > 0 && wp.element.createElement(
            "div",
            { style: { maxHeight: "400px", overflowY: "auto" } },
            suggestions.map(function(suggestion) {
              return wp.element.createElement(
                "div",
                {
                  key: suggestion.id,
                  style: {
                    marginBottom: "12px",
                    padding: "8px",
                    border: "1px solid #ddd",
                    borderRadius: "4px"
                  }
                },
                wp.element.createElement("strong", null, suggestion.title),
                suggestion.entities_matched && wp.element.createElement(
                  "div",
                  { style: { fontSize: "11px", color: "#666", marginTop: "4px" } },
                  "Entities: " + suggestion.entities_matched.join(", ")
                )
              );
            })
          )
        )
      );
    }
  });
})(window.wp);
