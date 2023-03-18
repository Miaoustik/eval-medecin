import React, {useCallback, useState} from "react";
import useInputGroupListWithSuggestions from "./Hooks/useInputGroupListWithSuggestions";
import InputGroupListWithSuggestions from "./Components/InputGroupListWithSuggestions";

export default function App ({ ingredients }) {

    const props = useInputGroupListWithSuggestions(['quantity', 'name'], 'name', ingredients)

    const [stages, setStages] = useState([])




    return (
        <>
            <h2 className="secondTitle text-decoration-underline">Créer une recette</h2>
            <form>
                <label className="form-label" htmlFor="title">Titre de la recette : </label>
                <input type="text" id="title" className="form-control"/>

                <label className="form-label" htmlFor="message">Description de la recette : </label>
                <textarea id="message" className="form-control" rows="5"></textarea>

                <p>Régimes : </p>

                <h3 className="mt-5 mb-3 secondTitle text-decoration-underline">Allergies du patient : </h3>
                <div className="ps-5 py-1 form-check">
                    <input className="form-check-input" type="checkbox" id="fruit"/>
                    <label htmlFor="fruit" className="form-check-label text-secondary textNoto ps-2">Fruits de
                        mer</label>
                </div>
                <div className="ps-5 py-1 form-check">
                    <input className="form-check-input" type="checkbox" id="Noix"/>
                    <label htmlFor="Noix" className="form-check-label text-secondary textNoto ps-2">Noix</label>
                </div>
                <div className="ps-5 py-1 form-check">
                    <input className="form-check-input" type="checkbox" id="Lactose"/>
                    <label htmlFor="Lactose" className="form-check-label text-secondary textNoto ps-2">Lactose</label>
                </div>
                <div className="ps-5 py-1 form-check">
                    <input className="form-check-input" type="checkbox" id="Gluten"/>
                    <label htmlFor="Gluten" className="form-check-label text-secondary textNoto ps-2">Gluten</label>
                </div>

                <h3 className="mt-5 mb-3 secondTitle text-decoration-underline">Regime du patient : </h3>
                <div className="ps-5 py-1 form-check">
                    <input className="form-check-input" type="checkbox" id="sel"/>
                    <label htmlFor="sel" className="form-check-label text-secondary textNoto ps-2">Sans sel</label>
                </div>
                <div className="ps-5 py-1 form-check">
                    <input className="form-check-input" type="checkbox" id="sucre"/>
                    <label htmlFor="sucre" className="form-check-label text-secondary textNoto ps-2">Sans sucre</label>
                </div>
                <div className="ps-5 py-1 form-check">
                    <input className="form-check-input" type="checkbox" id="Végétarien"/>
                    <label htmlFor="Végétarien"
                           className="form-check-label text-secondary textNoto ps-2">Végétarien</label>
                </div>
                <div className="ps-5 py-1 form-check">
                    <input className="form-check-input" type="checkbox" id="graisse"/>
                    <label htmlFor="graisse" className="form-check-label text-secondary textNoto ps-2">Pauvre en
                        graisse</label>
                </div>

                <label className="form-label" htmlFor="preparation">Temps de préparation : </label>
                <input type="number" id="preparation" className="form-control"/>

                <label className="form-label" htmlFor="repos">Temps de repos : </label>
                <input type="number" id="repos" className="form-control"/>

                <label className="form-label" htmlFor="cuisson">Temps de cuisson : </label>
                <input type="number" id="cuisson" className="form-control"/>

                <p>Ingrédients : </p>

                <InputGroupListWithSuggestions {...props} />
            </form>
        </>
    )
}