import React, {useCallback, useState} from "react";
import IngredientInput from "./Components/IngredientInput";
import useInputGroupList from "./Hooks/useInputGroupList";

export default function App ({ ingredients }) {

    const {inputs, handleChange, handleAdd, handleRemove, setInputs} = useInputGroupList(['quantity', 'name'])

    const [stages, setStages] = useState([])

    const [suggestions, setSuggestions] = useState([])
    const [suggestionActive, setSuggestionActive] = useState(null);

    const filterIngredient = useCallback((value) => {
        const ingredientFiltred = JSON.parse(ingredients).filter(e => {
            const regex = new RegExp('^' + value.charAt(0).toUpperCase() + value.slice(1))
            return regex.test(e.name)

        })
        setSuggestions(ingredientFiltred)
    }, [])

    const handleChange2 = useCallback((e) => {
        e.preventDefault()
        handleChange(e)
        const value = e.target.value
        filterIngredient(value)
    }, [handleChange, ingredients])


    //TODO NEED ID
    const handleFocus = useCallback((e) => {
        setSuggestionActive(e.target.getAttribute('data-id'))
    }, [])

    const handleBlur = useCallback((e) => {

        setSuggestionActive(null)
    }, [])


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

                {inputs.map((value, index) => {
                    return (<IngredientInput filterIngredient={filterIngredient} setInputs={setInputs} handleBlur={handleBlur} key={value.id} index={index} handleChange={handleChange2} withBtn={inputs.length > 1} suggestionActive={suggestionActive} suggestions={suggestions} handleFocus={handleFocus} value={inputs[index]} handleRemove={handleRemove} />)
                })}

                <button className={'shadow1 btn btn-primary text-white w-100 mt-4'} onClick={handleAdd}>Ajouter un ingredient</button>
            </form>
        </>
    )
}