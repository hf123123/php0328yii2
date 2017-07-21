<table>
    <tr>
        <th>ID：</th>
        <th>详细内容：</th>
    </tr>

    <?php foreach ($models as $model2):?>



        <tr>
            <td><?=$models->id?></td>
        </tr>
        <tr>
            <td><?=$models->content?></td>
        </tr>
    <?php endforeach;?>



</table>