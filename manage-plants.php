<?php
$db = Database::getInstance();

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['add_plant'])) {
        // Add new plant
        $db->query(
            "INSERT INTO hydrogen_plants (name, capacity_mw, status, lat, lng) 
             VALUES (?, ?, ?, ?, ?)",
            [$_POST['name'], $_POST['capacity_mw'], $_POST['status'], $_POST['lat'], $_POST['lng']]
        );
    } elseif (isset($_POST['edit_plant'])) {
        // Update plant
        $db->query(
            "UPDATE hydrogen_plants 
             SET name = ?, capacity_mw = ?, status = ?, lat = ?, lng = ? 
             WHERE id = ?",
            [$_POST['name'], $_POST['capacity_mw'], $_POST['status'], $_POST['lat'], $_POST['lng'], $_POST['id']]
        );
    } elseif (isset($_GET['delete'])) {
        // Delete plant
        $db->query("DELETE FROM hydrogen_plants WHERE id = ?", [$_GET['delete']]);
    }
}
require_once 'php/security-headers.php';
// Get all plants
$plants = $db->fetchAll("SELECT * FROM hydrogen_plants ORDER BY name");
?>

<h2>Manage Hydrogen Plants</h2>

<!-- Add New Plant Form -->
<div style="background: #f9f9f9; padding: 15px; border-radius: 8px; margin-bottom: 20px;">
    <h3>Add New Plant</h3>
    <form method="POST">
        <div class="form-group">
            <label for="name">Plant Name</label>
            <input type="text" id="name" name="name" required>
        </div>
        
        <div class="form-group">
            <label for="capacity_mw">Capacity (MW)</label>
            <input type="number" id="capacity_mw" name="capacity_mw" step="0.01" required>
        </div>
        
        <div class="form-group">
            <label for="status">Status</label>
            <select id="status" name="status" required>
                <option value="planned">Planned</option>
                <option value="under_construction">Under Construction</option>
                <option value="operational">Operational</option>
            </select>
        </div>
        
        <div class="form-group">
            <label for="lat">Latitude</label>
            <input type="number" id="lat" name="lat" step="0.000001" required>
        </div>
        
        <div class="form-group">
            <label for="lng">Longitude</label>
            <input type="number" id="lng" name="lng" step="0.000001" required>
        </div>
        
        <button type="submit" name="add_plant" class="btn-primary">Add Plant</button>
    </form>
</div>

<!-- Plants List -->
<h3>Existing Plants</h3>
<table class="data-table">
    <thead>
        <tr>
            <th>Name</th>
            <th>Capacity (MW)</th>
            <th>Status</th>
            <th>Location</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($plants as $plant): ?>
        <tr>
            <td><?php echo htmlspecialchars($plant['name']); ?></td>
            <td><?php echo $plant['capacity_mw']; ?></td>
            <td><?php echo ucfirst(str_replace('_', ' ', $plant['status'])); ?></td>
            <td><?php echo $plant['lat'] . ', ' . $plant['lng']; ?></td>
            <td>
                <a href="?page=plants&edit=<?php echo $plant['id']; ?>">Edit</a> | 
                <a href="?page=plants&delete=<?php echo $plant['id']; ?>" onclick="return confirm('Are you sure?')">Delete</a>
            </td>
        </tr>
        <?php endforeach; ?>
    </tbody>
</table>

<?php
// Edit form if requested
if (isset($_GET['edit'])) {
    $plantId = $_GET['edit'];
    $plant = $db->fetchOne("SELECT * FROM hydrogen_plants WHERE id = ?", [$plantId]);
    
    if ($plant):
?>
<div style="position: fixed; top: 50%; left: 50%; transform: translate(-50%, -50%); 
            background: white; padding: 20px; border-radius: 8px; box-shadow: 0 4px 6px rgba(0,0,0,0.1); 
            z-index: 1000; width: 400px;">
    <h3>Edit Plant</h3>
    <form method="POST">
        <input type="hidden" name="id" value="<?php echo $plant['id']; ?>">
        
        <div class="form-group">
            <label for="edit_name">Plant Name</label>
            <input type="text" id="edit_name" name="name" value="<?php echo htmlspecialchars($plant['name']); ?>" required>
        </div>
        
        <div class="form-group">
            <label for="edit_capacity">Capacity (MW)</label>
            <input type="number" id="edit_capacity" name="capacity_mw" step="0.01" value="<?php echo $plant['capacity_mw']; ?>" required>
        </div>
        
        <div class="form-group">
            <label for="edit_status">Status</label>
            <select id="edit_status" name="status" required>
                <option value="planned" <?php echo $plant['status'] === 'planned' ? 'selected' : ''; ?>>Planned</option>
                <option value="under_construction" <?php echo $plant['status'] === 'under_construction' ? 'selected' : ''; ?>>Under Construction</option>
                <option value="operational" <?php echo $plant['status'] === 'operational' ? 'selected' : ''; ?>>Operational</option>
            </select>
        </div>
        
        <div class="form-group">
            <label for="edit_lat">Latitude</label>
            <input type="number" id="edit_lat" name="lat" step="0.000001" value="<?php echo $plant['lat']; ?>" required>
        </div>
        
        <div class="form-group">
            <label for="edit_lng">Longitude</label>
            <input type="number" id="edit_lng" name="lng" step="0.000001" value="<?php echo $plant['lng']; ?>" required>
        </div>
        
        <button type="submit" name="edit_plant" class="btn-primary">Update Plant</button>
        <a href="?page=plants" style="margin-left: 10px;">Cancel</a>
    </form>
</div>
<?php
    endif;
}
?>