import { Component, OnInit } from '@angular/core';
import { FormBuilder, FormGroup, ReactiveFormsModule, Validators } from '@angular/forms';
import { ActivatedRoute, Router } from '@angular/router';
import { PokemonService } from '../../services/pokemon.service';

@Component({
  selector: 'app-pokemon-form',
  templateUrl: './pokemon-form.component.html',
  standalone: true,
  imports: [ReactiveFormsModule],
  styleUrls: ['./pokemon-form.component.css']
})
export class PokemonFormComponent implements OnInit {
  pokemonForm: FormGroup;
  isEdit: boolean = false;
  pokemonId: number | null = null;

  constructor(
    private fb: FormBuilder,
    private pokemonService: PokemonService,
    private route: ActivatedRoute,
    private router: Router
  ) {
    this.pokemonForm = this.fb.group({
      name: ['', Validators.required],
      height: ['', [Validators.required, Validators.min(1)]],
      weight: ['', [Validators.required, Validators.min(1)]],
      types: ['', Validators.required],
      abilities: ['', Validators.required],
      imageUrl: [''],
    });
    console.log('Constructeur exécuté');
  }

  ngOnInit(): void {
    this.pokemonId = this.route.snapshot.params['id'];
    if (this.pokemonId) {
      this.isEdit = true;
      this.loadPokemon();
    }
  }

  loadPokemon(): void {
    this.pokemonService.getPokemonById(this.pokemonId!).subscribe(data => {
      this.pokemonForm.patchValue({
        name: data.name,
        height: data.height,
        weight: data.weight,
        types: data.types.join(', '),
        abilities: data.abilities.join(', '),
        imageUrl: data.imageUrl
      });
    }, error => {
      console.error('Erreur lors du chargement du Pokémon:', error);
    });
  }

  onSubmit(): void {
    if (this.pokemonForm.invalid) {
      return;
    }

    const pokemonData = { ...this.pokemonForm.value };
    console.log('Données envoyées au service:', { ...pokemonData, id: this.pokemonId! });


    pokemonData.types = pokemonData.types.split(',').map((type: string) => type.trim());
    pokemonData.abilities = pokemonData.abilities.split(',').map((ability: string) => ability.trim());


    if (this.isEdit) {
      this.pokemonService.editPokemon({ ...pokemonData, id: this.pokemonId! })
        .subscribe(response => {
          console.log('Réponse de l\'API lors de la modification :', response);
          console.log('Pokémon modifié avec succès');
          this.router.navigate(['/']);
        }, error => {
          console.error('Erreur lors de la modification du Pokémon:', error);
        });
    } else {
      this.pokemonService.addPokemon(pokemonData)
        .subscribe(() => {
          console.log('Pokémon ajouté avec succès');
          this.router.navigate(['/']);
        }, error => {
          console.error('Erreur lors de l\'ajout du Pokémon:', error);
        });
    }
    console.log('Le formulaire a été soumis');
  }
}
