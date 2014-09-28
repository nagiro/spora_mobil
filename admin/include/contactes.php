<h2>Contactes</h2>

<div class="actions">
    <a href="?page=contacte" class="button">Afegeix un contacte</a>
</div>

<table>
    <thead>
        <th>Nom</th>
        <th>Tipus</th>
        <th>Adreça</th>
        <th>Accions</th>
    </thead>
    <tbody>
        <?php
            $contactList = new DBTable('contactes');
            $contactList = $contactList->readAll();

            foreach($contactList as $contact) {
                echo '<tr>
                    <td>' . $contact['nom'] . '</td>
                    <td>' . $contact['tipusContacte'] . '</td>
                    <td>' . $contact['tipusAdresa'] . ': ' . $contact['adresa'] . ' ' . $contact['numero'] . ', ' . $contact['pis'] . ' ' . $contact['porta'] . '</td>
                    <td>
                        <a href="?page=contacte&amp;id=' . $contact['id'] . '" class="button">Edita</a>
                        <a href="?action=deleteContact&amp;id=' . $contact['id'] . '" class="button">Esborra</a>
                    </td>
                </tr>';
            }
        ?>
    </tbody>
</table>