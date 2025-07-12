<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Contact;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use App\Livewire\StalledCustomers;

class StalledCustomersTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Create a test user
        $this->user = User::factory()->create();
        $this->actingAs($this->user);
    }

    public function test_stalled_customers_page_loads()
    {
        $response = $this->get(route('stalled-customers'));
        $response->assertStatus(200);
    }

    public function test_stalled_customers_component_renders()
    {
        Livewire::test(StalledCustomers::class)
            ->assertStatus(200)
            ->assertSee('Stalled Customers')
            ->assertSee('Customers that haven\'t been contacted recently');
    }

    public function test_identifies_stalled_customers()
    {
        // Create contacts with different contact dates
        $stalledContact = Contact::factory()->create([
            'last_contacted_at' => now()->subDays(45),
            'first_name' => 'John',
            'last_name' => 'Stalled',
            'email' => 'john.stalled@example.com'
        ]);

        $recentContact = Contact::factory()->create([
            'last_contacted_at' => now()->subDays(5),
            'first_name' => 'Jane',
            'last_name' => 'Recent',
            'email' => 'jane.recent@example.com'
        ]);

        $neverContactedContact = Contact::factory()->create([
            'last_contacted_at' => null,
            'first_name' => 'Bob',
            'last_name' => 'Never',
            'email' => 'bob.never@example.com'
        ]);

        Livewire::test(StalledCustomers::class)
            ->set('stalledDays', 30)
            ->assertSee('John Stalled')
            ->assertSee('Bob Never')
            ->assertDontSee('Jane Recent');
    }

    public function test_filtering_by_sales_rep()
    {
        $salesRep1 = User::factory()->create(['name' => 'Sales Rep 1']);
        $salesRep2 = User::factory()->create(['name' => 'Sales Rep 2']);

        $contact1 = Contact::factory()->create([
            'last_contacted_at' => now()->subDays(45),
            'owner_id' => $salesRep1->id,
            'first_name' => 'Contact',
            'last_name' => 'One'
        ]);

        $contact2 = Contact::factory()->create([
            'last_contacted_at' => now()->subDays(45),
            'owner_id' => $salesRep2->id,
            'first_name' => 'Contact',
            'last_name' => 'Two'
        ]);

        Livewire::test(StalledCustomers::class)
            ->set('selectedOwner', $salesRep1->id)
            ->set('stalledDays', 30)
            ->assertSee('Contact One')
            ->assertDontSee('Contact Two');
    }

    public function test_search_functionality()
    {
        $contact1 = Contact::factory()->create([
            'last_contacted_at' => now()->subDays(45),
            'first_name' => 'John',
            'last_name' => 'Doe',
            'email' => 'john.doe@example.com'
        ]);

        $contact2 = Contact::factory()->create([
            'last_contacted_at' => now()->subDays(45),
            'first_name' => 'Jane',
            'last_name' => 'Smith',
            'email' => 'jane.smith@example.com'
        ]);

        Livewire::test(StalledCustomers::class)
            ->set('search', 'John')
            ->set('stalledDays', 30)
            ->assertSee('John Doe')
            ->assertDontSee('Jane Smith');
    }

    public function test_mark_as_contacted_functionality()
    {
        $contact = Contact::factory()->create([
            'last_contacted_at' => now()->subDays(45),
            'first_name' => 'Test',
            'last_name' => 'Contact'
        ]);

        $this->assertNotNull($contact->last_contacted_at);
        $this->assertTrue($contact->last_contacted_at->lt(now()->subDays(30)));

        Livewire::test(StalledCustomers::class)
            ->call('updateLastContacted', $contact->id);

        $contact->refresh();
        $this->assertTrue($contact->last_contacted_at->isToday());
    }

    public function test_stalled_days_filter()
    {
        $contact = Contact::factory()->create([
            'last_contacted_at' => now()->subDays(20),
            'first_name' => 'Test',
            'last_name' => 'Contact'
        ]);

        // With 30 days filter, should NOT show the contact
        Livewire::test(StalledCustomers::class)
            ->set('stalledDays', 30)
            ->assertDontSee('Test Contact');

        // With 14 days filter, should show the contact
        Livewire::test(StalledCustomers::class)
            ->set('stalledDays', 14)
            ->assertSee('Test Contact');
    }
}