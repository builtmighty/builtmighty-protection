<?php
/**
 * Database.
 * 
 * @since   1.0.0
 * @author  Built Mighty
 */
namespace BuiltMightyProtect;
class builtProtectionDB {

    /**
     * Variables.
     * 
     * @since   1.0.0
     */
    public $db;
    public $table;

    /**
     * Construct.
     * 
     * @since   1.0.0
     */
    public function __construct() {

        // Get database.
        global $wpdb, $table_prefix;

        // Set variables.
        $this->db           = $wpdb;
        $this->prefix       = $table_prefix;
        $this->protect      = $table_prefix . 'built_protect';
        $this->whitelist    = $table_prefix . 'built_whitelist';

    }

    /**
     * Request.
     * 
     * @param   string  $query  Query to be executed. When referencing the table, you can freely use wp_ as the prefix and if it's a different prefix, the code with compensate.
     * @param   string  $type   Type of request. Can be results, row, or var.
     * 
     * @since   1.0.0
     */
    public function request( $query, $type ) {

        // Switch between types.
        switch( $type ) {

            // Results.
            case 'results':
                return $this->db->get_results( $query, ARRAY_A );
                break;

            // Row.
            case 'row':
                return $this->db->get_row( $query, ARRAY_A );
                break;

            // Var.
            case 'var':
                return $this->db->get_var( $query, ARRAY_A );
                break;

            // Default.
            default:
                return $this->db->get_results( $query, ARRAY_A );
                break;

        }

    }

    /**
     * Insert.
     * 
     * @param   array   $data   Data to insert.
     * 
     * @since   1.0.0
     */
    public function insert( $table, $data ) {

        // Insert.
        $this->db->insert( $table, $data );

    }

    /**
     * Update.
     * 
     * @param   array   $data   Data to update.
     * @param   array   $where  Where to update.
     * 
     * @since   1.0.0
     */
    public function update( $table, $data, $where ) {

        // Update.
        $this->db->update( $table, $data, $where );

    }

    /**
     * Delete.
     * 
     * @param   array   $data   Where to delete.
     * 
     * @since   1.0.0
     */
    public function delete( $table, $data ) {

        // Delete.
        $this->db->delete( $table, $data );

    }

    /**
     * Create table.
     * 
     * @since   1.0.0
     */
    public function create_table() {

        // Globals.
        global $table_prefix, $wpdb;

        // Loop through tables.
        foreach( $this->get_schema() as $table => $columns ) {

            // Set table.
            $table = $table_prefix . $table;

            // Creat table, if it doesn't exist.
            if( $wpdb->get_var( "SHOW TABLES LIKE '$table'" ) != $table ) {

                // Set SQL.
                $sql = "CREATE TABLE $table (";

                // Loop.
                foreach( $columns as $column => $setting ) {

                    // Add to SQL.
                    $sql .= "\n`" . $column . "` " . $setting . ',';

                }

                // Finish SQL.
                $sql .= "PRIMARY KEY  (id)
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8;";

                // Require.
                require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );

                // Create table.
                dbDelta( $sql );

            }

        }

    }

    /**
     * Set table columns.
     * 
     * @since   1.0.0
     */
    public function get_schema() {

        // Set.
        $structure = [
            'built_protect'     => [
                'id'        => 'int(11) NOT NULL AUTO_INCREMENT',
                'ip'        => 'varchar(255) NOT NULL',
                'order_id'  => 'int(11) NOT NULL',
                'date'      => 'datetime NOT NULL',
            ],
            'built_whitelist'   => [
                'id'        => 'int(11) NOT NULL AUTO_INCREMENT',
                'ip'        => 'varchar(255) NOT NULL',
                'date'      => 'datetime NOT NULL',
            ]
        ];

        // Return.
        return $structure;

    }

}