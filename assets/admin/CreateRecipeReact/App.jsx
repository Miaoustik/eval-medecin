import React, {useCallback, useEffect, useMemo, useRef, useState} from "react";
import InputGroupList from "./Components/InputGroupList";
import useInputGroupList from "./Hooks/useInputGroupList";
import useInputCheckbox from "./Hooks/useInputCheckbox";
import InputCheckbox from "./Components/InputCheckbox";
import {capitalizeFirstLetter} from "./Utils/functions";

export default function App ({recipeid = null}) {

    const dataRef = useRef({
        diets: null,
        allergens: null,
        error: false
    })


    useEffect(() => {

        setLoading(true)


        function setCheckbox (prevState, values, checkedValues = null) {
            const newState = []
            values.forEach((v) => {
                newState.push({
                    id: v.id,
                    name: v.name,
                    checked: false
                })
            })
            if (checkedValues) {
                checkedValues.forEach((v) => {
                    newState.forEach((e) => {
                        if (e.id == v.id) {
                            e.checked = true
                        }
                    })
                })
            }


            return newState
        }

        const controller = new AbortController()

        if (recipeid) {
            const fetchRecipeOption = {
                signal: controller.signal,
                headers: {
                    Accept: 'application/json'
                }
            }

            fetch('/admin/modifier-recette/api/getdata/' + recipeid, fetchRecipeOption)
                .then(res => res.json())
                .then(dataArray => {
                    console.log(dataArray[0])
                    const data = dataArray[0]
                    const diets = dataArray[1]
                    const allergens = dataArray[2]

                    dataRef.current = {
                        diets,
                        allergens
                    }

                    setTitleInput(data.title)
                    setDescriptionInput(data.description)
                    setPreps( prevState => {

                        const newState = {...prevState}

                        newState.preparation = data.preparationTime
                        newState.repos = data.breakTime
                        newState.cuisson = data.cookingTime

                        return newState
                    })



                    allergensCheckbox.setInputs(prevState => {
                        return setCheckbox(prevState, allergens, data.allergens)
                    })

                    dietsCheckbox.setInputs(prevState => {
                        return setCheckbox(prevState, diets, data.diets)
                    })

                    ingredientsInputGroup.setInputs(prevState => {
                        const newState = [...prevState]
                        data.ingredientRecipes.forEach((value, index) => {
                            if (index === 1) {
                                newState[0].quantity = value.quantity
                                newState[0].name = value.ingredient.name
                                newState[0].realId = value.id
                                newState[0].error = ''
                            } else {
                                newState.push({
                                    id: newState[newState.length - 1].id + 1,
                                    quantity: value.quantity,
                                    name: value.ingredient.name,
                                    realId: value.id,
                                    error: ''
                                })
                            }
                        })
                        return newState
                    })

                    stagesInputGroup.setInputs(prevState => {
                        const newState = [...prevState]
                        data.stages.forEach((value, index) => {
                            if (index === 1) {
                                newState[0].stage = value
                            } else {
                                newState.push({
                                    id: newState[newState.length - 1].id + 1,
                                    stage: value
                                })
                            }
                        })
                        return newState
                    })
                })
                .finally(() => setLoading(false))
        } else {
            const fetchOption = {
                signal: controller.signal,
                headers: {
                    Accept: 'application/json'
                }
            }
            fetch('/admin/creer-recette/api/getdata', fetchOption)
                .then(res => res.json())
                .then(dataArray => {
                    allergensCheckbox.setInputs(prevState => {
                        return setCheckbox(prevState, dataArray[1])
                    })

                    dietsCheckbox.setInputs(prevState => {
                        return setCheckbox(prevState, dataArray[1])
                    })
                })
                .catch(e => console.log(e))
                .finally(() => setLoading(false))
        }


        return () => {
            controller.abort();
        }

    }, [])

    const [loading, setLoading] = useState(false)
    const [titleInput, setTitleInput] = useState('')
    const [descriptionInput, setDescriptionInput] = useState('')

    const ingredientsInputGroup = useInputGroupList(['quantity', 'name'])
    const stagesInputGroup = useInputGroupList(['stage'])
    const allergensInputGroup = useInputGroupList(['name'], false)
    const dietsInputGroup = useInputGroupList(['name'], false)

    const dietsCheckbox = useInputCheckbox([])

    const allergensCheckbox = useInputCheckbox([])

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

        //dietinputforeach

        function checkDuplicate ( inputs, checkInputs) {
            inputs.inputs.forEach((i, k) => {
                checkInputs.inputs.forEach(c => {
                    if (i.name === c.name) {
                        inputs.setInputs(prevState => {
                            const newState = [...prevState]
                            newState[k].error = "L'entité éxistes déjà."
                            return newState
                        })
                    }
                })
            })
        }

        checkDuplicate(dietsInputGroup, dietsCheckbox)

        const recipe = {
            'title' : titleInput,
            'description' : descriptionInput,
            'preparationTime' : preps.preparation === '' ? 0 : parseInt(preps.preparation, 10),
            'breakTime' : preps.repos === '' ? 0 : parseInt(preps.repos, 10),
            'cookingTime' : preps.cuisson === '' ? 0 : parseInt(preps.cuisson, 10),
            "ingredientRecipes" : ingredientsInputGroup.inputs.map(e => {

                const value = {
                    quantity: e.quantity,
                    ingredient: {
                        name: e.name
                    }
                }

                if (e.realId) {
                    value['id'] = e.realId
                    value.recipe = recipeid
                }

                return value

            }),
            "stages" : stagesInputGroup.inputs.map(e => e.stage),
            'allergens' : [],
            'diets' : []
        }

        if (recipeid) {
            recipe['id'] = recipeid
        }

        allergensCheckbox.inputs.forEach(input => {

            if (input.checked === true) {
                recipe.allergens.push(input.id)
            }
        })

        console.log(recipe.allergens)

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
                recipe.diets.push(input.id)
            }
        })

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

        if (dataRef.current.error === false) {
            fetch('/admin/modifier-recette/api/modify', requestOptions)
                .then(res => res.json())
                .then(data => console.log(data))
                .catch(e => console.log(e))

            setSubmitted(true)
        } else {
            setTrySubmitted(false);
        }


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


    if (loading) {
        return <p>Chargement...</p>
    }

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