(function(wp) {
    var el = wp.element.createElement;
    var PluginDocumentSettingPanel = wp.editPost.PluginDocumentSettingPanel;
    var TextareaControl = wp.components.TextareaControl;
    var useSelect = wp.data.useSelect;
    var useDispatch = wp.data.useDispatch;

    function IbericodeSEOPanel() {
        var meta = useSelect(function(select) {
            return select('core/editor').getEditedPostAttribute('meta') || {};
        }, []);

        var editPost = useDispatch('core/editor').editPost;

        var description = meta['_ibericode_seo_description'] || '';

        return el(
            PluginDocumentSettingPanel,
            {
                name: 'ibericode-seo-panel',
                title: 'SEO Settings',
                icon: 'search'
            },
            el(
                TextareaControl,
                {
                    label: 'Meta Description',
                    help: 'Maximum 160 characters recommended. Current length: ' + description.length,
                    value: description,
                    onChange: function(value) {
                        editPost({
                            meta: Object.assign({}, meta, { _ibericode_seo_description: value })
                        });
                    },
                    maxLength: 160,
                    rows: 3
                }
            )
        );
    }

    wp.plugins.registerPlugin('ibericode-seo', {
        render: IbericodeSEOPanel,
        icon: ''
    });
})(window.wp);
