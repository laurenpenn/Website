            <div class="sidebarRight">
                <ul>
                    <?php if ( !function_exists('dynamic_sidebar')  
                    || !dynamic_sidebar( 'Sidebar' ) ) : ?>  
                    <h2>About</h2>  
                    <p>This is the deafult sidebar, add some widgets to change it.</p>  
                    <?php endif; ?>
                </ul>
            </div>
