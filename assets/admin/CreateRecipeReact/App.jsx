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
        error: 0
    })

    const controllerRef = useRef(null)

    const [loading, setLoading] = useState(false)
    const [titleInput, setTitleInput] = useState('')
    const [descriptionInput, setDescriptionInput] = useState('')

    const dietsCheckbox = useInputCheckbox([])

    const allergensCheckbox = useInputCheckbox([])

    const [createdId, setCreatedId] = useState(null);

    const ingredientsInputGroup = useInputGroupList(['quantity', 'name'])
    const stagesInputGroup = useInputGroupList(['stage'])
    const allergensInputGroup = useInputGroupList(['name'], false, true)
    const dietsInputGroup = useInputGroupList(['name'], false, true)

    const [postLoading, setPostLoading] = useState(false)

    const [trySubmitted, setTrySubmitted] = useState(false)
    const [submitted, setSubmitted] = useState(false)

    const [preps, setPreps] = useState({
        'preparation': '',
        'repos': '',
        'cuisson': '',
    })

    function setCheckbox(prevState, values, checkedValues = null) {
        const newState = []
        values.forEach((v) => {
            newState.push({
                id: v.id,
                name: v.name,
                checked: false
            })
        })
        if (checkedValues) {

            if (checkedValues instanceof Array) {
                checkedValues.forEach((v) => {
                    newState.forEach((e) => {
                        if (e.id == v.id) {
                            e.checked = true
                        }
                    })
                })
            } else {
                for (const v in checkedValues) {
                    newState.forEach((e) => {
                        if (e.id == v.id) {
                            e.checked = true
                        }
                    })
                }
            }
        }
        return newState
    }

    function setRecipe(dataArray, setTitleInput, setDescriptionInput, setPreps, allergensCheckbox, dietsCheckbox, ingredientsInputGroup, stagesInputGroup, dataRef) {

        const data = dataArray[0]
        const diets = dataArray[1]
        const allergens = dataArray[2]

        dataRef.current = {
            diets,
            allergens,
            error: 0
        }

        setTitleInput(data.title)
        setDescriptionInput(data.description)
        setPreps(prevState => {

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
    }

    //TODO FINIR VALIDATION ETAPE INGREDIENTS

    useEffect(() => {

            controllerRef.current = new AbortController()

            setLoading(true)

            if (recipeid) {
                const fetchRecipeOption = {
                    signal: controllerRef.current.signal,
                    headers: {
                        Accept: 'application/json'
                    }
                }

                fetch('/admin/modifier-recette/api/getdata/' + recipeid, fetchRecipeOption)
                    .then(res => res.json())
                    .then(dataArray => {
                        setRecipe(dataArray, setTitleInput, setDescriptionInput, setPreps, allergensCheckbox, dietsCheckbox, ingredientsInputGroup, stagesInputGroup, dataRef)

                    })
                    .finally(() => setLoading(false))
            } else {
                const fetchOption = {
                    signal: controllerRef.current.signal,
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
                            return setCheckbox(prevState, dataArray[0])
                        })
                    })
                    .catch(e => console.log(e))
                    .finally(() => setLoading(false))
            }


            return () => {
                controllerRef.current.abort();
            }

    }, [])

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
        setTrySubmitted(false)
        setSubmitted(false)
        setTitleInput('')
        setDescriptionInput('')

        allergensCheckbox.setInputs(prevState => {
            const newState = [...prevState]
            newState.forEach(e => e.checked = false)
            return newState
        })

        allergensInputGroup.setInputs(prevState => {
            const newState = [...prevState][0]
            newState.id = 1
            newState.error = ''
            allergensInputGroup.inputNames.forEach(e => {
                newState[e] = ''
            })
            return [newState]
        })

        dietsCheckbox.setInputs(prevState => {
            const newState = [...prevState]
            newState.forEach(e => e.checked = false)
            return newState
        })

        dietsInputGroup.setInputs(prevState => {
            const newState = [...prevState][0]
            newState.id = 1
            newState.error = ''
            dietsInputGroup.inputNames.forEach(e => {
                newState[e] = ''
            })
            return [newState]
        })

        setPreps(prevState => {
            const newState = {...prevState}
            newState.cuisson = ''
            newState.preparation = ''
            newState.repos = ''
            return newState
        })
        ingredientsInputGroup.setInputs(prevState => {
            const newState = [...prevState][0]
            newState.id = 1
            newState.error = ''
            return [newState]
        })
        stagesInputGroup.setInputs(prevState => {
            const newState = [...prevState][0]
            newState.id = 1
            newState.error = ''
            return [newState]
        })

        const fetchOption = {
            headers: {
                Accept: 'application/json',
            }
        }

        fetch('/admin/creer-recette/api/getdata', fetchOption)
            .then(res => res.json())
            .then(dataArray => {
                allergensCheckbox.setInputs(prevState => {
                    return setCheckbox(prevState, dataArray[1])
                })

                dietsCheckbox.setInputs(prevState => {
                    return setCheckbox(prevState, dataArray[0])
                })
            })
            .catch(e => console.log(e))
    }, [])

    const handleTrySubmit = useCallback((e) => {
        e.preventDefault()
        setTrySubmitted(true)
        setSubmitted(false)
    }, [])

    const handleTryNo = useCallback(() => {
        setTrySubmitted(false)
    }, [])

    function checkDuplicate ( inputs, checkInputs) {

        function setErrorFunc (error, inputs, k){
            if (error === '') {
                inputs.setInputs(prevState => {
                    const newState = [...prevState]

                    if (newState[k].name !== '') {
                        if (newState[k].error === "L'entité éxistes déjà.") {
                            dataRef.current.error--
                        }
                        newState[k].error = 'good'
                    } else {
                        if (newState[k].error !== '') {
                            dataRef.current.error--
                        }
                        newState[k].error = ''
                    }

                    return newState
                })
            }
        }

        inputs.inputs.forEach((i, k) => {
            let error = ''
            checkInputs.inputs.forEach(c => {
                if (i.name.toLowerCase() === c.name.toLowerCase()) {
                    error = "L'entité éxistes déjà."
                    if (inputs[k].error !== error) {
                        dataRef.current.error++
                        inputs.setInputs(prevState => {

                            const newState = [...prevState]
                            newState[k].error = error
                            return newState
                        })
                    }


                }
            })

            return setErrorFunc(error, inputs, k)
        })

        inputs.inputs.forEach((i, k) => {
            let error = ''
            inputs.inputs.forEach((c, index) => {
                if (i.name.toLowerCase() === c.name.toLowerCase() && k !== index) {
                    error = "L'entité éxistes déjà."
                    dataRef.current.error++
                    inputs.setInputs(prevState => {

                        const newState = [...prevState]
                        newState[k].error = error
                        return newState
                    })
                }
            })
            return setErrorFunc(error, inputs, k)
        })

    }

    const handleBlurDiet = useCallback(() => {

        checkDuplicate(dietsInputGroup, dietsCheckbox)
    }, [dietsInputGroup, dietsCheckbox, checkDuplicate])

    const handleBlurAllergen = useCallback(() => {
        checkDuplicate(allergensInputGroup, allergensCheckbox)
    }, [allergensInputGroup, allergensCheckbox, checkDuplicate])

    const handleTryYes = useCallback((e) => {
        e.preventDefault()
        setPostLoading(true)
        setSubmitted(false)

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
            body: JSON.stringify(recipe),
            signal: controllerRef.current.signal
        };

        console.log(recipe)

        if (dataRef.current.error === 0) {
            if (recipeid) {
                fetch('/admin/modifier-recette/api/modify', requestOptions)
                    .then(() => {
                        const fetchRecipeOption = {
                            signal: controllerRef.current.signal,
                            headers: {
                                Accept: 'application/json'
                            }
                        }

                        fetch('/admin/modifier-recette/api/getdata/' + recipeid , fetchRecipeOption)
                            .then(res => res.json())
                            .then(dataArray => {
                                setRecipe(dataArray, setTitleInput, setDescriptionInput, setPreps, allergensCheckbox, dietsCheckbox, ingredientsInputGroup, stagesInputGroup, dataRef)
                                setSubmitted(true)
                                setPostLoading(false)
                            })
                    })
                    .catch(e => console.log(e))
            } else {
                console.log(recipe)
                fetch('/admin/creer-recette/api/create', requestOptions)
                    .then(res => res.json())
                    .then ((data) => {
                        setCreatedId(data)
                        setSubmitted(true)
                        setPostLoading(false)
                    })
                    .catch(e => console.log(e))
            }



        } else {
            console.log('hello', dataRef.current.error)
            setPostLoading(false)
            setTrySubmitted(false);
        }


    }, [
        titleInput,
        descriptionInput,
        preps,
        ingredientsInputGroup.inputs,
        stagesInputGroup.inputs,
        allergensCheckbox.inputs,
        dietsCheckbox.inputs,
        setTitleInput, setDescriptionInput, setPreps, allergensCheckbox, dietsCheckbox, ingredientsInputGroup, stagesInputGroup, dataRef
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
            <h2 className="secondTitle text-decoration-underline">{recipeid ? 'Modifier' : 'Créer'} une recette</h2>
            <form>
                <label className="form-label text-secondary mt-4" htmlFor="title">Titre de la recette : </label>
                <input onChange={handleTitleInput} value={titleInput} type="text" id="title" className="form-control"/>

                <label className="form-label text-secondary mt-4" htmlFor="message">Description de la recette : </label>
                <textarea onChange={handleDescriptionInput} value={descriptionInput} id="message" className="form-control" rows="5"></textarea>

                <h3 className="mt-5 mb-3 secondTitle text-decoration-underline">Allergies de la recette : </h3>

                <InputCheckbox {...allergensCheckbox} />
                <InputGroupList  {...allergensInputGroup} handleBlur={handleBlurAllergen} btnAddText={'Créer et ajouter une allergie'} />

                <h3 className="mt-5 mb-3 secondTitle text-decoration-underline">Regimes de la recette : </h3>

                <InputCheckbox {...dietsCheckbox}  />
                <InputGroupList {...dietsInputGroup} handleBlur={handleBlurDiet} btnAddText={'Créer et ajouter un régime'} />

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
                                    <p className={'p-0 m-0 textNoto'}>Votre recette a bien été {!recipeid ? 'crée' : 'modifiée'}.</p>
                                </div>

                                {createdId
                                    ? <a href={'/admin/modifier-recette/' + createdId} className={'btn btn-primary text-white w-100 my-3 shadow1'}>Modifier la recette</a>
                                    : <button onClick={handleTrySubmit} className={'btn btn-primary text-white w-100 my-3 shadow1'}>Modifier la recette</button>
                                }

                                <p className={'textNoto'}>Voulez-vous créer une nouvelle recette ?</p>
                                {recipeid
                                    ? <a href={'/admin/creer-recette'} className={'btn btn-primary text-white w-100 text-decoration-none shadow1'}>Créer une nouvelle recette</a>
                                    : <button onClick={handleResetForm} className={'btn btn-primary text-white w-100 shadow1'}>Créer une nouvelle recette</button>
                                }

                            </>
                        ) : (

                            <>
                                {trySubmitted && postLoading &&
                                    <div className={'alert alert-success shadow1 mt-4'}>
                                        <p className={'p-0 m-0 textNoto'}>Votre recette est en cours de {recipeid ? 'modification' : 'création'}.</p>
                                    </div>
                                }
                                {postLoading === false &&
                                    <>
                                        <p className={'textNoto mt-3'}>La recette va être {recipeid ? 'modifiée' : 'crée'}. Etes-vous sur? </p>
                                        <div className={'d-inline-flex w-100'}>
                                            <button type={'submit'} onClick={handleTryYes} className={'btn btn-primary text-white me-3 w-100 shadow1'}>Oui</button>
                                            <button onClick={handleTryNo} className={'btn btn-secondary w-100 shadow1'}>Non</button>
                                        </div>
                                    </>

                                }

                            </>
                        )}

                    </>
                ) : (
                    <button onClick={handleTrySubmit} className={'btn btn-primary text-white w-100 mt-5 shadow1'}>{recipeid ? 'Modifier la recette' : 'Créer la recette'}</button>
                )}
            </form>
        </>
    )
}