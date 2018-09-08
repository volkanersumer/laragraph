# LARAVEL GRAPH API

## READING TRANSACTIONS

### READING RECORDS
```php
$records = Graph::match(
    [
        'n' => [
            'Person',
            ['name' => 'volkan', 'lastname' => 'ersumer']
        ]
    ]
)->get();

echo $records[0]->value('n')->name;  //volkan
```
### READING ONE RECORD

```php
$records = Graph::match(
    [
        'n' => [
            'Person',
            ['name' => 'volkan', 'lastname' => 'ersumer']
        ]
    ]
)->first();

echo $record->value('n')->name;  //volkan
```

### READING WITH WHERE QUERY
```php
$records = Graph::match(
    [
        'n' => [
            'Person',
            ['name' => 'volkan', 'lastname' => 'ersumer']
        ]
    ]
)->where(['n.lastname' => 'ersumer'])->get();

echo $records[0]->value('n')->name;  //volkan
```

### READING WITH EDGES
```php
$records = Graph::match(
    [
        'n' => [
            'Person',
            ['name' => 'volkan', 'lastname' => 'ersumer']
        ]
    ]
)
->where(['n.lastname' => 'ersumer'])
->edge([
    'n' => [
        'out',
        'LIKED',
        'Post'
    ],
    'n' => [
        'out',
        'POSTED',
        'POST' => [
            'in',
            'LIKED',
            'Person'
        ]
    ]
])
->get();
```

##CREATING TRANSACTIONS

###CREATING NODES
```php
$p = Graph::create([
    'nodes' => [
        'n:Person' => [
            'name' => 'volkan',
            'lastname' => 'ersumer'
        ]
    ]
]);
```

##CREATING EDGES
```php
$p = Graph::match(['n' => 'Person', 'm' => 'Post'])->where(['n.name' => 'volkan', 'm.id' => '32'])->create([
    'edges' => [
        'n' => [
            'out',
            'LIKED'
            'm'
        ]
    ]
]);
```
