<?php
/**
 * The template for displaying 404 pages (not found).
 *
 * @link https://codex.wordpress.org/Creating_an_Error_404_Page
 *
 * @package flatsome
 */

get_header(); ?>
	<?php do_action('flatsome_before_404') ;?>
	<div id="primary" class="content-area">
		<main id="main" class="site-main container pt" role="main">
			<section class="error-404 not-found mt mb">
				<div class="row">
					<div class="col medium-3"><span class="header-font" style="font-size: 6em; font-weight: bold; opacity: .3">404</span></div>
					<div class="col medium-9">
						<header class="page-title">
							<h3 class="page-title">Rất tiếc . Trang bạn tìm kiếm không tồn tại trong hệ thống </h3>
						</header><!-- .page-title -->

						<div class="page-content">
							<p>Đường dẫn bạn đang truy cập không chính xác. Vui lòng quay lại trang chủ hoặc tìm kiếm trang phù hợp.</p>

							<?php get_search_form(); ?>

						</div><!-- .page-content -->
					</div>
				</div><!-- .row -->
				
				
			</section><!-- .error-404 -->

		</main><!-- #main -->
	</div><!-- #primary -->
	<?php do_action('flatsome_after_404') ;?>
<?php get_footer(); ?>
