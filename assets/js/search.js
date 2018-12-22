$('#article_filter_tags').select2({
    theme: 'bootstrap',
    placeholder: 'Choose tags...'
});

$('#article_filter_author').select2({
    theme: 'bootstrap',
    ajax: {
        url: '/api/author/list',
        delay: 250, // wait 250 before the request
        data: function (params) {
            return {
                q: params.term,
            };
        },
        processResults: function (resp) {
            return {
                results: resp.type === 'success' ? resp.data : []
            };
        }
    }
});