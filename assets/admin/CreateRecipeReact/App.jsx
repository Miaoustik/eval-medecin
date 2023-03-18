import React, {useCallback, useState} from "react";
import useInputGroupListWithSuggestions from "./Hooks/useInputGroupListWithSuggestions";
import InputGroupListWithSuggestions from "./Components/InputGroupListWithSuggestions";
import InputGroupList from "./Components/InputGroupList";
import useInputGroupList from "./Hooks/useInputGroupList";

export default function App ({ ingredients, diets, allergens }) {

    const ingredientsInputGroup = useInputGroupListWithSuggestions(['quantity', 'name', 'test'], 'name', ingredients)
    const stagesInputGroup = useInputGroupList(['stage'])
    const allergensInputGroup = useInputGroupList(['name'], false)
    const dietsInputGroup = useInputGroupList(['name'], false)

    const [preps, setPreps] = useState({
        'preparation': '',
        'repos': '',
        'cuisson': '',
    })

    const handleChange = useCallback((e) => {
        const name = e.target.getAttribute('id')
        const value = e.target.value
        setPreps(prevState => {
            const newState = {...prevState}
            newState[name] = value
            return newState
        })
    }, [])

    function parseIntPreps(value) {
        return parseInt(value === '' ? 0 : value, 10)
    }

    return (
        <>
            <h2 className="secondTitle text-decoration-underline">Créer une recette</h2>
            <form>
                <label className="form-label text-secondary mt-4" htmlFor="title">Titre de la recette : </label>
                <input type="text" id="title" className="form-control"/>

                <label className="form-label text-secondary mt-4" htmlFor="message">Description de la recette : </label>
                <textarea id="message" className="form-control" rows="5"></textarea>

                <h3 className="mt-5 mb-3 secondTitle text-decoration-underline">Allergies de la recette : </h3>

                {JSON.parse(allergens).map((v) => {
                    return (
                        <div key={'allergen' + v.name} className="ps-5 py-1 form-check">
                            <input className="form-check-input" type="checkbox" id={v.name}/>
                            <label htmlFor={v.name} className="form-check-label text-secondary textNoto ps-2">{v.name}</label>
                        </div>
                    )
                })}

                <InputGroupList {...allergensInputGroup} btnAddText={'Créer et ajouter une allergie'} />

                <h3 className="mt-5 mb-3 secondTitle text-decoration-underline">Regimes de la recette : </h3>

                {JSON.parse(diets).map((v) => {
                    return (
                        <div key={'diet' + v.name} className="ps-5 py-1 form-check">
                            <input className="form-check-input" type="checkbox" id={v.name}/>
                            <label htmlFor={v.name} className="form-check-label text-secondary textNoto ps-2">{v.name}</label>
                        </div>
                    )
                })}

                <InputGroupList {...dietsInputGroup} btnAddText={'Créer et ajouter un régime'} />

                <h3 className="mt-5 mb-3 secondTitle text-decoration-underline">Temps de la recette : </h3>

                <p>Temps total : {(parseIntPreps(preps.preparation) + parseIntPreps(preps.repos) + parseIntPreps(preps.cuisson))} minutes</p>

                <label className="form-label mt-4 text-secondary" htmlFor="preparation">Temps de préparation : </label>
                <input onChange={handleChange} type="number" id="preparation" className="form-control" value={preps["preparation"]}/>

                <label className="form-label text-secondary mt-3" htmlFor="repos">Temps de repos : </label>
                <input onChange={handleChange} type="number" id="repos" className="form-control" value={preps["repos"]}/>

                <label className="form-label text-secondary mt-3" htmlFor="cuisson">Temps de cuisson : </label>
                <input onChange={handleChange} type="number" id="cuisson" className="form-control" value={preps["cuisson"]}/>

                <h3 className="mt-5 mb-3 secondTitle text-decoration-underline">Ingrédients : </h3>

                <InputGroupListWithSuggestions {...ingredientsInputGroup} />

                <h3 className="mt-5 mb-3 secondTitle text-decoration-underline">Etapes : </h3>

                <InputGroupList {...stagesInputGroup} btnAddText={'Ajouter une étape'} type={'textarea'} />
                <button className={'btn btn-primary text-white w-100 mt-5'}>Créer la recette</button>
            </form>
        </>
    )
}