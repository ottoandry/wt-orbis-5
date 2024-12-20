<?php

$s = filter_input( INPUT_GET, 's', FILTER_SANITIZE_STRING );

$has_advanced = is_post_type_archive( 'orbis_person' ) || is_post_type_archive( 'orbis_project' );

$action_url = '';

if ( is_post_type_archive() ) {
	$action_url = orbis_get_post_type_archive_link();
}

$sorting_terms = [
	'author'   => esc_html__( 'Author', 'orbis-4' ),
	'date'     => esc_html__( 'Date', 'orbis-4' ),
	'modified' => esc_html__( 'Modified', 'orbis-4' ),
	'title'    => esc_html__( 'Title', 'orbis-4' ),
];

/*
 * add specific sorting terms per post type here
 */
switch ( get_query_var( 'post_type' ) ) {
	case 'orbis_subscription':
		$sorting_terms[] = '-';

		$sorting_terms['active_subscriptions'] = esc_html__( 'Active Subscriptions', 'orbis-4' );
		break;

	case 'orbis_project':
		$sorting_terms[] = '-';

		$sorting_terms['project_finished_modified']       = esc_html__( 'Modified and Finished', 'orbis-4' );
		$sorting_terms['project_invoice_number']          = esc_html__( 'Invoice Number', 'orbis-4' );
		$sorting_terms['project_invoice_number_modified'] = esc_html__( 'Invoice Number Modified', 'orbis-4' );
		break;

	default:
		break;
}

?>
<div class="card-body">
	<form method="get" action="<?php echo esc_attr( $action_url ); ?>">
		<div class="d-flex justify-content-between">

			<div class="row row-cols-lg-auto g-3 align-items-center">
				<div class="col-12">
					<label for="orbis_search_query" class="sr-only"><?php esc_html_e( 'Search', 'orbis-4' ); ?></label>

					<input id="orbis_search_query" type="search" class="form-control" name="s" placeholder="<?php esc_attr_e( 'Search', 'orbis-4' ); ?>" value="<?php echo esc_attr( $s ); ?>">
				</div>

				<div class="col-12">
					<button type="submit" class="btn btn-secondary"><?php esc_html_e( 'Search', 'orbis-4' ); ?></button>
				</div>

				<?php if ( is_post_type_archive( 'orbis_person' ) ) : ?>

					<div class="col-12">
						<?php

						$slugs = filter_input( INPUT_GET, 'c', FILTER_SANITIZE_STRING );
						$slugs = explode( ',', $slugs );

						$terms = get_terms(
							[
								'taxonomy' => 'orbis_person_category',
							] 
						);

						printf(
							'<select name="%s" class="select2" multiple="multiple" style="width: 30em;" placeholder="%s">',
							esc_attr( 'c[]' ),
							esc_attr__( 'All Categories', 'orbis-4' )
						);

						foreach ( $terms as $term ) {
							printf(
								'<option value="%s" %s">%s</option>',
								esc_attr( $term->term_id ),
								selected( in_array( $term->slug, $slugs, true ), true, false ),
								esc_html( $term->name )
							);
						}

						echo '</select>';

						?>

						<style type="text/css">
							.select2-choices {
								background-image: none;

								border: 1px solid rgba(0, 0, 0, 0.15);
								border-radius: 0.25rem;
							}
						</style>

						<button type="submit" class="btn btn-secondary"><?php esc_html_e( 'Filter', 'orbis-4' ); ?></button>
					</div>

				<?php endif; ?>

				<?php if ( $has_advanced ) : ?>

					<div class="col-12">
						<small><a href="#" class="advanced-search-link" data-toggle="collapse" data-target="#advanced-search"><?php esc_html_e( 'Advanced Search', 'orbis-4' ); ?></a></small>
					</div>

				<?php endif; ?>
			</div>

			<div class="form-inline">
				<div class="dropdown show ml-1">
					<button class="btn btn-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
						<?php

						//phpcs:disable
						$orderby   = ( isset( $_GET['orderby'] ) ) ? $sorting_terms[$_GET['orderby']] : '';
						$sort_text = ( $orderby ) ? $orderby : esc_html__( 'Sort by…', 'orbis' );
						//phpcs:enable
						echo esc_html( $sort_text );

						if ( isset( $_GET['order'] ) ) {
							$order = orbis_invert_sort_order( sanitize_text_field( wp_unslash( $_GET['order'] ) ) );
							echo ' ' . wp_kses_post( orbis_sorting_icon( $order ) );
						}
						?>
					</button>

					<ul class="dropdown-menu">

						<?php
						foreach ( $sorting_terms as $sorting_term => $label ) {
							if ( '-' === $label ) {
								echo '<div class="dropdown-divider"></div>';

								continue;
							}

							$classes = [
								'dropdown-item',
								'clearfix',
							];

							$orderby = ( isset( $_GET['orderby'] ) ) ? $_GET['orderby'] : ''; // phpcs:ignore
							$order   = orbis_get_sort_order( $sorting_term );

							if ( $sorting_term === $orderby ) {
								$classes[] = 'active';

								$icon = orbis_sorting_icon( $order );
							} else {
								$icon = '';
							}

							$order = orbis_invert_sort_order( $order );

							$link = add_query_arg(
								[
									'orderby' => $sorting_term,
									'order'   => $order,
								] 
							);

							printf(
								"<li><a class='%s' href='%s'> %s %s </a></li>",
								esc_attr( implode( ' ', $classes ) ),
								esc_url( $link ),
								esc_html( $label ),
								wp_kses_post( $icon )
							);
						}
						?>

					</ul>
				</div>

				<?php if ( is_post_type_archive( 'orbis_person' ) ) : ?>

					<div>

						<?php

						$csv_url = add_query_arg( $_GET, get_post_type_archive_link( 'orbis_person' ) . 'csv' );
						$xls_url = add_query_arg( $_GET, get_post_type_archive_link( 'orbis_person' ) . 'xls' );

						?>
						<div class="dropdown">
							<button class="btn btn-secondary dropdown-toggle ml-1" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><?php esc_html_e( 'Download', 'orbis-4' ); ?></button>

							<div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
								<a class="dropdown-item" href="<?php echo esc_url( $xls_url ); ?>" target="_blank"><i class="fa fa-file-excel-o" aria-hidden="true"></i> <?php esc_html_e( 'Excel', 'orbis-4' ); ?></a>
								<a class="dropdown-item" href="<?php echo esc_url( $csv_url ); ?>" target="_blank"><i class="fa fa-file-text-o" aria-hidden="true"></i> <?php esc_html_e( 'CSV', 'orbis-4' ); ?></a>
							</div>
						</div>
					</div>

				<?php endif; ?>
			</div>
			<?php get_template_part( 'templates/filter', get_query_var( 'post_type' ) ); ?>
		</div>

		<?php get_template_part( 'templates/filter_advanced', get_query_var( 'post_type' ) ); ?>
	</form>
</div>
