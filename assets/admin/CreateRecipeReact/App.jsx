import React, {useCallback, useMemo, useState} from "react";
import InputGroupList from "./Components/InputGroupList";
import useInputGroupList from "./Hooks/useInputGroupList";
import useInputCheckbox from "./Hooks/useInputCheckbox";
import InputCheckbox from "./Components/InputCheckbox";
import {capitalizeFirstLetter} from "./Utils/functions";

export default function App ({ diets, allergens }) {

    //TODO title description state

    const [titleInput, setTitleInput] = useState('')
    const [descriptionInput, setDescriptionInput] = useState('')

    const ingredientsInputGroup = useInputGroupList(['quantity', 'name'])
    const stagesInputGroup = useInputGroupList(['stage'])
    const allergensInputGroup = useInputGroupList(['name'], false)
    const dietsInputGroup = useInputGroupList(['name'], false)

    const dietsParse = useMemo(() => {
        return JSON.parse(diets)
    }, [diets])

    const dietsCheckbox = useInputCheckbox(dietsParse)

    const allergensParse = useMemo(() => {
        return JSON.parse(allergens)
    }, [allergens])

    const allergensCheckbox = useInputCheckbox(allergensParse)

    const [trySubmitted, setTrySubmitted] = useState(false)
    const [submitted, setSubmitted] = useState(false)

    const [preps, setPreps] = useState({
        'preparation': '',
        'repos': '',
        'cuisson': '',
    })

    const handleTitleInput = useCallback((e) => {
        setTitleInput(e.target.value)
    }, [])

    const handleDescriptionInput = useCallback((e) => {
        setDescriptionInput(e.target.value)
    }, [])


    const handleChange = useCallback((e) => {
        const name = e.target.getAttribute('id')
        const value = e.target.value
        setPreps(prevState => {
            const newState = {...prevState}
            newState[name] = value
            return newState
        })
    }, [])

    const handleResetForm = useCallback((e) => {
        e.preventDefault()
        window.scrollTo(0, 0)

        //TODO Vider les champs apres le submit

    }, [])

    const handleTrySubmit = useCallback((e) => {
        e.preventDefault()
        setTrySubmitted(true)
    }, [])

    const handleTryNo = useCallback(() => {
        setTrySubmitted(false)
    }, [])

    const handleTryYes = useCallback((e) => {

        e.preventDefault()

        const recipe = {
            'title' : titleInput,
            'description' : descriptionInput,
            'preparationTime' : preps.preparation === '' ? 0 : parseInt(preps.preparation, 10),
            'breakTime' : preps.repos === '' ? 0 : parseInt(preps.repos, 10),
            'cookingTime' : preps.cuisson === '' ? 0 : parseInt(preps.cuisson, 10),
            "ingredients" : ingredientsInputGroup.inputs,
            "stages" : stagesInputGroup.inputs.map(e => e.stage),
            'allergens' : [],
            'diets' : []
        }

        allergensCheckbox.inputs.forEach(input => {
            if (input.checked === true) {
                recipe.allergens.push('/api/allergens/' + input.id)
            }
        })

        allergensInputGroup.inputs.forEach(input => {
            if (input.name !== '') {
                const value = input.name.trim()
                recipe.allergens.push({
                    'name': capitalizeFirstLetter(value)
                })
            }
        })

        dietsCheckbox.inputs.forEach(input => {
            if (input.checked === true) {
                recipe.diets.push('/api/diets/' + input.id)
            }
        })

        console.log(dietsInputGroup, allergensInputGroup)
        dietsInputGroup.inputs.forEach(input => {
            if (input.name !== '') {
                const value = input.name.trim()
                recipe.diets.push({
                    'name': capitalizeFirstLetter(value)
                })
            }
        })

        const requestOptions = {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(recipe)
        };

        console.log(recipe)

        /*fetch('/api/recipes', requestOptions)
            .then(res => res.json())
            .then(data => console.log(data))
            .catch(e => console.log(e))*/

        //TODO creer un state pour chaque input et les récuperer ici.
        //TODO BACK END creation recette

        setSubmitted(true)
    }, [
        titleInput,
        descriptionInput,
        preps,
        ingredientsInputGroup.inputs,
        stagesInputGroup.inputs,
        allergensCheckbox.inputs,
        dietsCheckbox.inputs
    ])

    function parseIntPreps(value) {
        return parseInt(value === '' ? 0 : value, 10)
    }

    //TODO patient Only

    return (
        <>
            <h2 className="secondTitle text-decoration-underline">Créer une recette</h2>
            <form>
                <label className="form-label text-secondary mt-4" htmlFor="title">Titre de la recette : </label>
                <input onChange={handleTitleInput} value={titleInput} type="text" id="title" className="form-control"/>

                <label className="form-label text-secondary mt-4" htmlFor="message">Description de la recette : </label>
                <textarea onChange={handleDescriptionInput} value={descriptionInput} id="message" className="form-control" rows="5"></textarea>

                <h3 className="mt-5 mb-3 secondTitle text-decoration-underline">Allergies de la recette : </h3>

                <InputCheckbox {...allergensCheckbox} />
                <InputGroupList  {...allergensInputGroup} btnAddText={'Créer et ajouter une allergie'} />

                <h3 className="mt-5 mb-3 secondTitle text-decoration-underline">Regimes de la recette : </h3>

                <InputCheckbox {...dietsCheckbox} />
                <InputGroupList {...dietsInputGroup} btnAddText={'Créer et ajouter un régime'} />

                <h3 className="mt-5 mb-3 secondTitle text-decoration-underline">Temps de la recette : </h3>

                <p className={'text-secondary text-decoration-underline'}>Temps total : {(parseIntPreps(preps.preparation) + parseIntPreps(preps.repos) + parseIntPreps(preps.cuisson))} minutes</p>

                <label className="form-label mt-4 text-secondary" htmlFor="preparation">Temps de préparation : </label>
                <input onChange={handleChange} type="number" id="preparation" className="form-control" value={preps["preparation"]}/>

                <label className="form-label text-secondary mt-3" htmlFor="repos">Temps de repos : </label>
                <input onChange={handleChange} type="number" id="repos" className="form-control" value={preps["repos"]}/>

                <label className="form-label text-secondary mt-3" htmlFor="cuisson">Temps de cuisson : </label>
                <input onChange={handleChange} type="number" id="cuisson" className="form-control" value={preps["cuisson"]}/>

                <h3 className="mt-5 mb-3 secondTitle text-decoration-underline">Ingrédients : </h3>

                <InputGroupList {...ingredientsInputGroup} placeholders={['5 grammes', 'Cannelle']} />

                <h3 className="mt-5 mb-3 secondTitle text-decoration-underline">Etapes : </h3>

                <InputGroupList {...stagesInputGroup} btnAddText={'Ajouter une étape'} type={'textarea'} />

                {trySubmitted ? (
                    <>

                        {submitted ? (
                            <>
                                <div className={'alert alert-success shadow1 mt-4'}>
                                    <p className={'p-0 m-0 textNoto'}>Votre recette a bien été crée.</p>
                                </div>
                                <p className={'textNoto'}>Voulez-vous créer une nouvelle recette ?</p>
                                <button onClick={handleResetForm} className={'btn btn-primary text-white w-100 shadow1'}>Créer une nouvelle recette</button>
                            </>
                        ) : (
                            <>
                                <p className={'textNoto mt-3'}>La recette va être crée. Etes-vous sur? </p>
                                <div className={'d-inline-flex w-100'}>
                                    <button type={'submit'} onClick={handleTryYes} className={'btn btn-primary text-white me-3 w-100 shadow1'}>Oui</button>
                                    <button onClick={handleTryNo} className={'btn btn-secondary w-100 shadow1'}>Non</button>
                                </div>
                            </>
                        )}

                    </>
                ) : (
                    <button onClick={handleTrySubmit} className={'btn btn-primary text-white w-100 mt-5 shadow1'}>Créer la recette</button>
                )}
            </form>
        </>
    )
}