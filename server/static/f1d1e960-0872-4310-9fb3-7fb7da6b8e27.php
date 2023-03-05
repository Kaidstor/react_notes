<?php

$getClient = statisticLink();

$statisticUrl = $getClient[0];
$client_id = $getClient[1]; 
get_header(null, [$statisticUrl]); 
$password = 'Ty63rs4aVqcnh2vUqRJTbNT26caRZJ';
/* Start the Loop */
while (have_posts()) {
	the_post(); ?>

	<div class="wrapper">
		<article id="post-<?php the_ID(); ?>">
			<?php
			the_title('<h2>', '</h2>');
			$file = get_field('файл');
			$text = get_field('текст');
			if ($file) { ?>
				<a target="blank" href="<?= wp_get_attachment_url($file); ?>">Прикреплённый файл</a>
			<? } ?>
			<div class="query__content">
				<?= $text ?>
			</div>
		</article>
		<?php
		$comments = get_comments(array(
			'post_id' => $post->ID,
			'number' => '3'
		));
		foreach ($comments as $comment) { ?>
			<div class="comment">
				<div class="comment__top">
					<p><?php echo $comment->comment_author; ?></p>
					<p class="comment__date"><?php echo $comment->comment_date; ?></p>
				</div>
				<p><?php echo $comment->comment_content; ?></p>
			</div>



		<? }


		// the_comments_pagination(
		// 	array(
		// 		'before_page_number' => esc_html__('Page', 'twentytwentyone') . ' ',
		// 		'mid_size'           => 0,
		// 		'prev_text'          => sprintf(
		// 			'%s <span class="nav-prev-text">%s</span>',
		// 			is_rtl() ? twenty_twenty_one_get_icon_svg('ui', 'arrow_right') : twenty_twenty_one_get_icon_svg('ui', 'arrow_left'),
		// 			esc_html__('Older comments', 'twentytwentyone')
		// 		),
		// 		'next_text'          => sprintf(
		// 			'<span class="nav-next-text">%s</span> %s',
		// 			esc_html__('Newer comments', 'twentytwentyone'),
		// 			is_rtl() ? twenty_twenty_one_get_icon_svg('ui', 'arrow_left') : twenty_twenty_one_get_icon_svg('ui', 'arrow_right')
		// 		),
		// 	)
		// ); 

		$args = [
			'title_reply' => 'Сообщение',
			'label_submit' => 'Отправить сообщение',
			'comment_field' => '<p class="comment-form-comment"><textarea id="comment" name="comment" cols="45" rows="8" aria-required="true"></textarea></p>',
			'logged_in_as'       => null,
			'fields' =>  array(
				'author' => '<p class="comment-form-author">
                    <label for="author">' . __('Name') . ($req ? ' <span class="required">*</span>' : '') . '</label>
                    <input id="author" name="author" type="text" value="' . esc_attr($commenter['comment_author']) . '" size="30"' . $aria_req . $html_req . ' />
                </p>',
				'email'  => '<p class="comment-form-email">
                    <label for="email">' . __('Email') . ($req ? ' <span class="required">*</span>' : '') . '</label>
                    <input id="email" name="email" ' . ($html5 ? 'type="email"' : 'type="text"') . ' value="' . esc_attr($commenter['comment_author_email']) . '" size="30" aria-describedby="email-notes"' . $aria_req . $html_req  . ' />
                </p>',
				'url'    => '<p class="comment-form-url">
                    <label for="url">' . __('Website') . '</label>
                    <input id="url" name="url" ' . ($html5 ? 'type="url"' : 'type="text"') . ' value="' . esc_attr($commenter['comment_author_url']) . '" size="30" />
                </p>',
			)
		];
		comment_form($args); ?>
	</div>
	<style>
		article>h2 {
			margin: 20px 0;
		}

		#comment {
			padding: 10px;
		}

		.comment {
			padding: 10px;
			background-color: white;
		}

		.comment__top {
			display: flex;
			align-items: center;
			margin: 0 0 10px 0;
		}

		.comment__date {
			padding: 4px;
			font-size: 12px;
			margin: 0 0 0 10px;
			background-color: gray;
			color: white;
			border-radius: 5px;
		}
	</style>
<?

}
get_footer();
