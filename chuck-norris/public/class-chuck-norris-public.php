<?php

/**
 * The public-facing functionality of the plugin.
 *
 * @link       https://wppb.me/
 * @since      1.0.0
 *
 * @package    Chuck_Norris
 * @subpackage Chuck_Norris/public
 */

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the public-facing stylesheet and JavaScript.
 *
 * @package    Chuck_Norris
 * @subpackage Chuck_Norris/public
 * @author     Miguel <miguel@blogscol.com>
 */
class Chuck_Norris_Public {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $plugin_name       The name of the plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;

		$this->init();
	}

    public function init()
    {
		$this->init_shortcodes();

		$this->init_actions();
    }

    public function init_shortcodes()
    {
		add_shortcode('chuck_norris_select_favorites', array($this,'shortcode_chuck_norris_select_favorites'));
		add_shortcode('chuck_norris_view_favorites', array($this,'shortcode_chuck_norris_view_favorites'));
    }

    public function init_actions()
    {
        add_action('wp_enqueue_scripts', array($this, 'enqueue_block_scripts_and_styles'));

        add_action('wp_ajax_save_favorite', array($this, 'save_favorite'));
        add_action('wp_ajax_nopriv_save_favorite', array($this, 'save_favorite'));

        add_action('wp_ajax_remove_favorite', array($this, 'remove_favorite'));
        add_action('wp_ajax_nopriv_remove_favorite', array($this, 'remove_favorite'));
    }

    public function enqueue_block_scripts_and_styles()
    {
		$this->enqueue_styles();
		$this->enqueue_scripts();
    }

	/**
	 * Register the stylesheets for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Chuck_Norris_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Chuck_Norris_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/chuck-norris-public.css', array(), $this->version, 'all' );

		wp_enqueue_style( "bootstrap", plugin_dir_url( __FILE__ ) . 'css/bootstrap.min.css', array(), $this->version, 'all' );

		wp_enqueue_style( "bootstrapGrid", plugin_dir_url( __FILE__ ) . 'css/bootstrap-grid.min.css', array(), $this->version, 'all' );

        wp_enqueue_style('sweetalert-css', plugin_dir_url( __FILE__ ) . 'js/sweetalert-master/lib/sweet-alert.min.css', '1.1.1');

        wp_enqueue_style('spinner-css', plugin_dir_url( __FILE__ ) . 'js/spinner/spin.css', '1.1.1');
	}

	/**
	 * Register the JavaScript for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Chuck_Norris_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Chuck_Norris_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */
		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/chuck-norris-public.js', array( 'jquery' ), $this->version, false );

        wp_localize_script($this->plugin_name, $this->plugin_name . '_ajax_vars', array('ajax_url' => admin_url('admin-ajax.php')));

		wp_enqueue_script( "bootstrap", plugin_dir_url( __FILE__ ) . 'js/bootstrap.min.js', array( 'jquery' ), $this->version, false );

        wp_enqueue_script('sweetalert-js', plugin_dir_url( __FILE__ ) . 'js/sweetalert-master/lib/sweet-alert.min.js', array('jquery'), '1.1.1');

        wp_enqueue_script('spinner-js', plugin_dir_url( __FILE__ ) . 'js/spinner/spin.js', array('jquery'), '1.1.1');
	}


	public function getCategories()
	{
		$categoriesURL ="https://api.chucknorris.io/jokes/categories";

		$data = file_get_contents($categoriesURL);
		return json_decode($data);
	}

	public function getData($categorie)
	{
		$categorieURL ="https://api.chucknorris.io/jokes/random?category=" . $categorie;

		$data = file_get_contents($categorieURL);

		return json_decode($data, true);
	}

	public function shortcode_chuck_norris_select_favorites($atts)
	{
	    echo '<div class="container">';
	    echo '<div class="row">';

 		for($i=0; $i<$atts['cant']; $i++)
 		{
	    	$this->displayElementsCategories($atts['category']); 			
 		}

	    echo '</div>';
	    echo '</div>';
	}

	public function displayElementsCategories($category)
	{
		$data = $this->getData($category);

		$this->displayElements($data, true, false);
	}

	public function shortcode_chuck_norris_view_favorites($atts)
	{
		$current_user = wp_get_current_user();

		if($current_user == false)
		{
		    echo '<div class="container">';
		    echo '<div class="row">';
		    echo '<h2>Para poder usar esta función tienes que estar logeado</h2>';
		    echo '</div>';
		    echo '</div>';
		}
		else
		{
			$favorites = get_user_meta( $current_user->ID, 'favorite', true );

		    echo '<div class="container">';
		    echo '<div class="row">';

			if ( empty( $favorites ) )
			    echo '<h2>No tienes favoritos guardados</h2>';
			else
			{
			    echo '<div class="col-md-12"><h2>Tus elementos favoritos</h2></div>';

				foreach($favorites as $favorito)
		 		{
			    	$this->displayElementsFavorites($favorito); 			
		 		}
			}

		    echo '</div>';
		    echo '</div>';
		}
	}

	public function displayElementsFavorites($data)
	{
		$this->displayElements($data, false, true);
	}

	public function displayElements($data, $showAddButton=false, $showRemoveButton=false)
	{
		?>
		<div class="col-md-4">
			<div class="card" style="width: 18rem;" id="<?php echo $data['id']; ?>">
			  <img class="card-img-top" src="<?php echo $data['icon_url']; ?>" alt="Card image cap">
			  <div class="card-body">
			    <p class="card-text"><?php echo $data['value']; ?></p>
				<?php
					if($showAddButton == true):
				?>
					<button type="button" class="btn btn-success add" data-id="<?php echo $data['id']; ?>" data-icon_url="<?php echo $data['icon_url']; ?>" data-url="<?php echo $data['url']; ?>" data-value="<?php echo $data['value']; ?>">Add</button>
				<?php
					endif;
				?>
				<?php
					if($showRemoveButton == true):
				?>
					<button type="button" class="btn btn-danger remove" data-id="<?php echo $data['id']; ?>">Remove</button>
				<?php
					endif;
				?>

			  </div>
			</div>
		</div>
		<?php
	}

    public function save_favorite()
    {
		$current_user = wp_get_current_user();

		if($current_user == false)
		{
	        $result['type']    = 'error';
	        $result['titulo']    = 'Usuario no existe';
			$result['mensaje'] = 'Para poder usar esta función tienes que estar logeado';

	        $result = json_encode($result);
	        echo $result;
	        wp_die();
		}
		else
		{
			$data = array
			(
			    'id' => $_REQUEST['id'],
			    'icon_url' => $_REQUEST['icon_url'],
			    'value' => $_REQUEST['value'],
			);

			$favorites = get_user_meta( $current_user->ID, 'favorite', true );

			if ( $favorites == null )
			{
				$favorites = array();

				$favorites[0] = $data;

			    add_user_meta( $current_user->ID, 'favorite', $favorites );
			}
			else
			{
				$found_key = array_search($data['id'], array_column($favorites, 'id'));

				if ( false === $found_key )
				{
					$favorites[] = $data;
				    update_user_meta( $current_user->ID, 'favorite', $favorites );
				}
				else
				{
			        $result['type']    = 'warning';
	        		$result['titulo']    = 'Ya existia ese favorito';
					$result['mensaje'] = 'Este elemento no se guardo porque ya existia en tus favoritos';

			        $result = json_encode($result);
			        echo $result;
			        wp_die();
				}

			}

	        $result['type']    = 'success';
       		$result['titulo']    = 'Favorito agregado';
			$result['mensaje'] = 'Tu favorito se guardo con éxito';

	        $result = json_encode($result);
	        echo $result;
	        wp_die();
		}
    }

    public function remove_favorite()
    {
		$current_user = wp_get_current_user();

		if($current_user == false)
		{
	        $result['type']    = 'error';
	        $result['titulo']    = 'Usuario no existe';
			$result['mensaje'] = 'Para poder usar esta función tienes que estar logeado';

	        $result = json_encode($result);
	        echo $result;
	        wp_die();
		}
		else
		{
			$favorites = get_user_meta( $current_user->ID, 'favorite', true );

			if ( empty( $favorites ) )
			{
		        $result['type']    = 'error';
		        $result['titulo']    = 'No tienes favoritos guardados';
				$result['mensaje'] = 'Para poder usar esta función es necesario tener algún favorito guardado';

		        $result = json_encode($result);
		        echo $result;
		        wp_die();
			}
			else
			{
				$found_key = array_search($_REQUEST['id'], array_column($favorites, 'id'));

				if ( false === $found_key )
				{
			        $result['type']    = 'warning';
		       		$result['titulo']    = 'No existe en tus favoritos';
					$result['mensaje'] = 'Este elemento no se puede eliminar ya que no existe en tus favoritos';

			        $result = json_encode($result);
			        echo $result;
			        wp_die();
				}
				else
				{
				    unset($favorites[$found_key]);
				    $reindex = array_values($favorites);
				    $favorites = $reindex;

				    if(count($favorites) == 0)
				    	delete_user_meta( $current_user->ID, 'favorite' );
				    else
				    	update_user_meta( $current_user->ID, 'favorite', $favorites );

			        $result['type']    = 'success';
		       		$result['titulo']    = 'Favorito eliminado';
					$result['mensaje'] = 'Tu favorito se elimino con exito';

			        $result = json_encode($result);
			        echo $result;
			        wp_die();
				}
			}
		}
    }
}