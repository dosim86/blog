$(document).ready(function () {
    $('.js-like-btn').on('click', function (e) {
        e.preventDefault();
        if ($(this).hasClass('not-allowed')) return false;

        let $this = $(this),
            $btnGroup = $this.closest('.js-like'),
            $like = $btnGroup.find('[data-type="1"] span'),
            $dislike = $btnGroup.find('[data-type="-1"] span'),
            token = $btnGroup.data('token')
        ;

        appGet($this.attr('href'), { token: token }).then(function(data) {
            if (data.type === 'success') {
                $like.html(data.data.likes);
                $dislike.html(data.data.dislikes);
            }
            notify(data.type, data.message);
        });
    });

    $('.js-bookmark-article-btn').on('click', function (e) {
        e.preventDefault();
        if ($(this).hasClass('not-allowed')) return false;

        let $this = $(this),
            $bookmarkValue = $this.find('span'),
            token = $this.data('token')
        ;

        appGet($this.attr('href'), { token: token }).then(function(data) {
            if (data.type === 'success') {
                $bookmarkValue.html(data.data);
            }
            notify(data.type, data.message);
        });
    });
});