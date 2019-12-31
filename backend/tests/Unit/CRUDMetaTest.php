<?php

namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class CRUDMetaTest extends TestCase
{

    public function testCreate()
    {
        $response = $this->withUser()->json('POST', '/api/v1.0/role', [
            'name' => 'TEST',
            'rights' => [
                ['tables' => '*', 'columns'=>'*', 'where'=>'*', 'actions'=>'TR1'],
                ['tables' => '*', 'columns'=>'*', 'where'=>'*', 'actions'=>'TR2'],
                ['tables' => '*', 'columns'=>'*', 'where'=>'*', 'actions'=>'TR3'],
            ],
            '_meta' => [
                'rights' => ['many'=>'right'],
            ],
        ]);

        $this->assertStatus($response);
    }

    function findRole() {
        $response = $this->withUser()->json('POST', '/api/v1.0/role/query', [
            'and' => [
                'name' => 'TEST'
            ],
            'with' => [
                'rights' => ['many'=>'right', 'query'=>['order'=>['id'=>'ASC']]],
            ],
        ]);
        $this->assertStatus($response);
        $roles = $response->json();
        $this->assertCount(1, $roles);
        $role = $roles[0];

        return $role;
    }

    public function testQuery()
    {
        $role = $this->findRole();
        $this->assertCount(3, $role['rights']);
        $this->assertEquals(true, $role['_meta']['rights']['ignore']);
        $this->assertEquals('right', $role['_meta']['rights']['many']);
    }

    public function testUpdate() {
        $role = $this->findRole();
        $role['rights'][2] = ['tables' => '*', 'columns'=>'*', 'where'=>'*', 'actions'=>'TR4'];
        $role['rights'][3] = ['tables' => '*', 'columns'=>'*', 'where'=>'*', 'actions'=>'TR5'];

        $role['_meta']['rights']['ignore'] = false;
        $this->withUser()->json('PUT', '/api/v1.0/role/' . $role['id'], $role);

        $role = $this->findRole();
        $this->assertCount(4, $role['rights']);
        $this->assertEquals('TR1', $role['rights'][0]['actions']);
        $this->assertEquals('TR2', $role['rights'][1]['actions']);
        $this->assertEquals('TR4', $role['rights'][2]['actions']);
        $this->assertEquals('TR5', $role['rights'][3]['actions']);
        $this->assertEquals(true, $role['_meta']['rights']['ignore']);
        $this->assertEquals('right', $role['_meta']['rights']['many']);
    }

    public function testDelete()
    {
        $role = $this->findRole();
        $rids = [];
        foreach ($role['rights'] as $right) $rids[] = $right['id'];
        $response = $this->withUser()->json('DELETE', '/api/v1.0/right/query?role_id=' . $role['id'], []);
        $response = $this->withUser()->json('DELETE', '/api/v1.0/role/' . $role['id'], []);

        $this->assertStatus($response);
    }

}
